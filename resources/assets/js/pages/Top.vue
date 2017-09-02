<template>
    <div id="page">
        <div id="contents">
            <h1 class="God" >God Paper</h1>
            <div id="form">
                Language:
                <select id="select-lang" v-model="request.language">
                    <option>おまかせ</option>
                    <option>C</option>
                    <option>C#</option>
                    <option>C++</option>
                    <option>Java</option>
                    <option>JavaScript</option>
                    <option>PHP</option>
                    <option>MySQL</option>
                    <option>Swift</option>
                </select>
                <input type="file" @change="toBlob">
                <button type="button">実行</button>
            </div>
            <div id="textarea">Response: <textarea id="response-text-area" v-model="request.source_code"></textarea></div>
            <div id="image">Image: <img :src="image" alt=""></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: '',
                request: {
                    language: 'おまかせ',
                    source_code: ''
                }
            }
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
                        jqXHR.setRequestHeader('Content-Type','application/json');
                        jqXHR.setRequestHeader('Ocp-Apim-Subscription-Key', subscriptionKey);
                    },
                    type: 'POST',
                    data: '{"url": ' + '"' + imageLink + '"}',
                })
                .done(data => {
                    console.log();
                    $('#response-text-area').val(this.jsonFormatting(data), null, 2);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
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
                return sourceCode;
            }
        }
    }
</script>

<style lang="scss" scoped>
    #response-text-area {
        display: block;
        width: 580px;
        height: 400px;
        font-size: 20px;
    }
    h1 {
        color:red;
        font-size:30px;
        padding:5px;
        text-align:center;
        border:5px 
    }
    #select-lang {
        border-top:solid 1.7px #808080;
        border-left:solid 1.7px #808080;
    }
    body{
        background: #f33;
        }
    #container {  
        position: relative;  
        width: 100%;
        max-width:1280px;
        margin: 0 auto;
        padding: 0;
    }
    #form{
        position: relative;
        width: 100%;
        max-width:1280px;
        margin:0 auto;
        padding:0;
    }
    
    
</style>

