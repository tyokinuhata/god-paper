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
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: '',
                request: {
                    source_code: '',
                    language: 'おまかせ'
                },
                extensions: {},
                nowExtension: 'hoge',
                result: '',
                writed: false,
                ran: false
            }
        },
        created() {
            $.get('http://localhost:8000/api/languagelist').then(
                    (data)=> {
                        this.$set(this, "extensions", Object.assign({"おまかせ": "おまかせ"}, (data)));
                    }
            );
        },
        methods: {
            uploadImage(formData) {
                $.ajax({
                    url: 'https://api.imgur.com/3/image',
                    type: 'POST',
                    datatype: 'json',
                    headers: {
                        Authorization: 'Client-ID 3094230e774a0ff',
                    },
                    data: formData,
                    success: response => {
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

                $.get('http://localhost:8000/api/language?code=' + encodeURIComponent(sourceCode)).then(
                        (data)=> {
                            console.log(data);
                            this.$set(this.request, "language", data);
                        }
                );

                // 言語判別ここまで

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
                code = encodeURIComponent(code);
                lang = encodeURIComponent(lang.toLowerCase());
                const postCode = axios.get('/api/create?source_code=' + code + '&language=' + lang)
                        .then(response => {
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

