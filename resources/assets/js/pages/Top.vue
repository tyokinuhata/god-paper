<template>
    <div id="page">
        <div id="contents">
            <h1>God Paper</h1>
            <div id="form">
                Language:
                <select>
                    <option>C</option>
                    <option>C#</option>
                    <option>C++</option>
                    <option>Java</option>
                    <option>JavaScript</option>
                    <option>PHP</option>
                    <option>MySQL</option>
                    <option>Swift</option>
                    <option>おまかせ</option>
                </select>
                <input type="file" @change="toBlob" id="file-select">
            </div>
            <div id="textarea">Response: <textarea id="response-text-area"></textarea></div>
            <div id="image">Image: <img :src="image" alt=""></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: ''
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
                .done(function(data) {
                    $('#response-text-area').val(JSON.stringify(data, null, 2));
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    var errorString = (errorThrown === '') ? 'Error. ' : errorThrown + ' (' + jqXHR.status + '): ';
                    errorString += (jqXHR.responseText === '') ? '' : (jQuery.parseJSON(jqXHR.responseText).message) ?
                        jQuery.parseJSON(jqXHR.responseText).message : jQuery.parseJSON(jqXHR.responseText).error.message;
                    alert(errorString);
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    * {
        margin: 0;
        padding: 0;
    }
    #contents {
        margin: 0 auto;
        width: 70%;
        input[type='file'] {
            display: inline-block;
        }
        #textarea {
            display: inline-block;
        }
        #image {
            display: inline-block;
        }
        #response-text-area {
            display: block;
            width: 580px;
            height: 400px;
        }
        #img {
            display: inline-block;
        }
    }
</style>