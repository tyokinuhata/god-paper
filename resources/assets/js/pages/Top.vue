<template>
    <div id="page">
        <div id="contents">
            <h1 class="God">God Paper</h1>
            <div id="form">
                Language:
                <select id="select-lang" v-model="request.language" @change="findExtension">
                    <option v-for="(text,val) in extensions">{{text}}</option>
                </select>
                <input type="file" @change="toBlob" id="file-select" :disabled="ran">
                <button type="button" v-on:click="paizaRun(request.source_code, request.language)" :disabled="!writed || ran">Running</button>
                <a href="" :download="nowExtension === undefined ? 'result.txt' : 'result.' + nowExtension" id="download-link" v-on:click="fileSave">Download</a>
            </div>
            <div id="text-image">
                <div id="textarea">Response: <textarea id="response-textarea" v-model="request.source_code"></textarea></div>
                <div id="image">Image: <img :src="image" alt=""></div>
            </div>
            <div>Result:
                <div>{{ result }}</div>
            </div>
            <audio style="display:none" id="welcome" preload="auto">
                <source src="/japari.mp3" type="audio/mp3">
            </audio>
            <div class="loading">
                <img src="/loading.gif" alt="読み込み">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: '',
                youkoso:'',
                request: {
                    source_code: '',
                    language: 'おまかせ'
                },
                extensions: {},
                nowExtension: 'hoge',
                result: '',
                writed: false,
                ran: false,
                reading: false,
                running: false
            }
        },
        created() {
            $.get('http://'+location.hostname+':'+location.port+'/api/languagelist').then(
                    (data)=> {
                        this.$set(this, "extensions", Object.assign({"おまかせ": "おまかせ"}, (data)));
                    }
            );
        },
        watch:{
            'request.source_code':(val)=>{
                console.log(val);
                if(val == "すごーい"){
                    document.getElementById("welcome").play();
                }
            },
        },
        methods: {
            uploadImage(formData) {
                $('.loading').addClass('active');
                $.ajax({
                    url: 'https://api.imgur.com/3/image',
                    type: 'POST',
                    datatype: 'json',
                    headers: {
                        Authorization: 'Client-ID 3094230e774a0ff',
                    },
                    data: formData,
                    success: response => {
                        $('.loading').removeClass('active');
                        this.processImage(response.data.link);
                        this.image = response.data.link;
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            },
            toBlob() {
                let blob = document.getElementById('file-select').files[0];
                let formData = new FormData();
                formData.append('image', blob);
                this.uploadImage(formData);
                this.writed = true;
            },
            processImage(imageLink) {
                $('.loading').addClass('active');
                let subscriptionKey = '548a4be3988240449b841c5ada938667';
                let uriBase = 'https://westcentralus.api.cognitive.microsoft.com/vision/v1.0/ocr';
                let params = {
                    language: 'unk',
                    detectOrientation: true
                };
                let url = uriBase + "?" + $.param(params);
                $.ajax({
                    url: url,
                    beforeSend: jqXHR => {
                        jqXHR.setRequestHeader('Content-Type', 'application/json');
                        jqXHR.setRequestHeader('Ocp-Apim-Subscription-Key', subscriptionKey);
                    },
                    type: 'POST',
                    data: '{"url": ' + '"' + imageLink + '"}',
                })
                        .done(data => {
                            $('.loading').removeClass('active');
                            console.log();
                            $('#response-textarea').val(this.jsonFormatting(data), null, 2);
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            var errorString = (errorThrown === '') ? 'Error. ' : errorThrown + ' (' + jqXHR.status + '): ';
                            errorString += (jqXHR.responseText === '') ? '' : (jQuery.parseJSON(jqXHR.responseText).message) ?
                                    jQuery.parseJSON(jqXHR.responseText).message : jQuery.parseJSON(jqXHR.responseText).error.message;
                            alert(errorString);
                        });
            },
            jsonFormatting(data) {
                let sourceCode = '';
                for (let region of data.regions) {
                    for (let line of region.lines) {
                        for (let word of line.words) {
                            sourceCode += word.text + ' ';
                        }
                    }
                }
                this.request.source_code = sourceCode;

                // ここから言語判別
                $('.loading').addClass('active');
                $.get('http://'+location.hostname+':'+location.port+'/api/language?code=' + encodeURIComponent(sourceCode)).then(
                        (data)=> {
                            console.log(data);
                            this.$set(this.request, "language", data);
                        }
                );

                // 言語判別ここまで
                $('.loading').removeClass('active');
                return sourceCode;
            },
            fileSave() {
                let text = document.getElementById('response-textarea').value;
                let blob = new File([text], 'result.txt');
                let downloadLink = document.getElementById('download-link');
                downloadLink.href = window.URL.createObjectURL(blob);
            },
            findExtension() {
                this.nowExtension = this.extensions[this.request.language];
            },
            paizaRun(code, lang) {
                $('.loading').addClass('active');
                code = encodeURIComponent(code);
                lang = encodeURIComponent(lang.toLowerCase());
                const postCode = axios.get('/api/create?source_code=' + code + '&language=' + lang)
                        .then(response => {
                            $('.loading').removeClass('active');
                            this.result = response.data.stdout;
                        })
                        .catch(err => {
                    });
                this.ran = true;
            }
        }
    }
</script>

<style lang="scss" scoped>

    .loading{
        display: none;
        opacity: 0;
        transition: opacity 1s ease;
        position: fixed;
        width: 600px;
        height:600px;
        top:0;
        bottom:0;
        left:0;
        right:0;
        margin:auto;
        img {
            width: 100%;
        }
    }
    .active{
        display: block;
        opacity: 1;
    }

    #response-textarea {
        display: block;
        width: 580px;
        height: 400px;
        font-size: 20px;
    }

    h1 {
        color: red;
        font-size: 30px;
        padding: 5px;
        text-align: center;
        border: 5px
    }

    #select-lang {
        border-top: solid 1.7px #808080;
        border-left: solid 1.7px #808080;
    }

    body {
        background: #f33;
    }

    #container {
        position: relative;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0;
    }

    #form {
        position: relative;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0;
        text-align: center;
    }
    .form-inner{
        display: inline-block;
        box-sizing: border-box;
        padding: 0 20px;
        margin: 0;
        width: auto;
        height: 30px;
    }
    #text-image{
        position: relative;
        width: 33%;
        max-width: 1280px;
        margin: 0 auto;
        flex-direction:row; 
    }
    #text-area{


    }
</style>
