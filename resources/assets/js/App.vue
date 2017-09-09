<template>
    <div class="container">
        <div class="header">
            <h1>God Paper</h1>
            <ul>
                <li>
                    <select v-model="request.language">
                        <option v-for="(text, val) in extensions">{{ text }}</option>
                    </select>
                </li>
                <li>
                    <label for="image-select">Image Select</label>
                    <input type="file" id="image-select" @change="toBlob">
                </li>
                <li>
                    <button type="button" v-on:click="paizaRun(request.source_code, request.language)">Run</button>
                </li>
                <li>
                    <a href="" id="download-link" :download="request.language === undefined || 'auto' ? 'result.txt' : 'result.' + request.language" v-on:click="fileSave">Download</a>
                </li>
            </ul>
        </div>
        <div class="content">
            <div class="sources">
                <div class="source-code">
                    <textarea id="response-data" placeholder="Here your code!" v-model="request.source_code"></textarea>
                </div>
                <div class="source-image">
                    <div>
                        <img :src="image" alt="">
                    </div>
                </div>
            </div>
            <div class="results">
                Result:
                <div class="result">{{ result }}</div>
            </div>
        </div>
        <audio style="display:none" id="welcome" preload="auto">
            <source src="/japari.mp3" type="audio/mp3">
        </audio>
        <audio style="display:none" id="great" preload="auto">
            <source src="/sugoi.mp3" type="audio/mp3">
        </audio>
        <!--<div class="loading">-->
        <!--<img src="/loading.gif" alt="読み込み">-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: '',
                request: {
                    source_code: '',
                    language: 'auto'
                },
                extensions: {},
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
                    this.$set(this, 'extensions', Object.assign({'auto': 'auto'}, (data)));
                }
            );
        },
        watch:{
            'request.source_code':(val)=>{
                document.getElementById("great").currentTime = 0;
                if(val === 'すごーい'){
                    document.getElementById('welcome').play();
                }
                document.getElementById("great").play();
            },
        },
        methods: {
            toBlob() {
                let blob = document.getElementById('image-select').files[0];
                let formData = new FormData();
                formData.append('image', blob);
                this.uploadImage(formData);
                this.writed = true;
            },
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
            processImage(imageLink) {
                $('.loading').addClass('active');
                let subscriptionKey = '548a4be3988240449b841c5ada938667';
                let uriBase = 'https://westcentralus.api.cognitive.microsoft.com/vision/v1.0/ocr';
                let params = {
                    language: 'unk',
                    detectOrientation: true
                };
                let url = uriBase + '?' + $.param(params);
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
                        this.$set(this.request, "language", data);
                    }
                );

                // 言語判別ここまで
                $('.loading').removeClass('active');
                return sourceCode;
            },
            fileSave() {
                let text = document.getElementById('response-data').value;
                let blob = new File([text], 'result.txt');
                let downloadLink = document.getElementById('download-link');
                downloadLink.href = window.URL.createObjectURL(blob);
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
    * {
        padding: 0;
        width: 100%;
    }
    .header {
        display: flex;
        align-items: center;
        padding: 15px;
        height: 10%;
        border-bottom: 1px solid #666;
        background: #fdd695;
        h1 {
            margin-right: auto;
            color: #6b4318;
        }
        ul {
            display: flex;
            list-style: none;
            width: 400px;
        }
        li {
            margin-top: 5px;
            height: 40px;
            margin-right: 10px;
        }
        select, label, button, a {
            padding: 10px;
            border: 1px solid #666;
            border-radius: 5px;
            background: #fdf4e8;
            color: #736d71;
            cursor: pointer;
            transition: 0.4s;
        }
        select:hover, label:hover, button:hover, a:hover {
            background: #6b4318;
            color: #fdf4e8;
        }
        select, button {
            outline: none;
        }
        select {
            width: 90px;
        }
        label {
            font-weight: normal;
            width: 110px;
        }
        #image-select {
            display: none;
        }
        button {
            width: 50px;
        }
        a {
            display: block;
            width: 90px;
            text-decoration: none;
        }
    }
    .content {
        height: 90%;
        background: #fdd695;
        .sources {
            margin: 0 auto;
            padding: 0;
            width: 70%;
            height: 70%;
            font-size: 0;
            .source-code {
                display: inline-block;
                margin: 0;
                margin-bottom: 50px;
                padding: 0;
                width: 50%;
                height: 80%;
                textarea {
                    display: block;
                    margin: 0;
                    padding: 0;
                    outline: none;
                    border: 0;
                    width: 100%;
                    height: 100%;
                    resize: none;
                    border-radius: 5px;
                    background: #fdf4e8;
                    font-size: 16px;
                }
            }
            .source-image {
                display: inline-block;
                margin: 0;
                padding: 20px;
                width: 50%;
                height: 100%;
                img {
                    display: block;
                    height: auto;
                    max-height: 100%;
                    width: auto;
                    max-width: 100%;
                    margin: 0 auto;
                }
            }
        }
        .results {
            margin: 0 auto;
            padding: 0;
            width: 70%;
            height: 30%;
            font-size: 16px;
            .result {
                background: #fdf4e8;
                height: 80%;
                border-radius: 5px;
                word-wrap: break-word;
            }
        }
        /*.loading {*/
        /*display: none;*/
        /*opacity: 0;*/
        /*transition: opacity 1s ease;*/
        /*position: fixed;*/
        /*width: 600px;*/
        /*height:600px;*/
        /*top:0;*/
        /*bottom:0;*/
        /*left:0;*/
        /*right:0;*/
        /*margin:auto;*/
        /*img {*/
        /*width: 100%;*/
        /*}*/
        /*}*/
        /*.active{*/
        /*display: block;*/
        /*opacity: 1;*/
        /*}*/
    }
</style>