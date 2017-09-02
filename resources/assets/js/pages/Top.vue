<template>
    <div id="page">
        <h1>God Paper</h1>
        <div>
            <input type="text" value="https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Atomist_quote_from_Democritus.png/338px-Atomist_quote_from_Democritus.png">
            <button v-on:click="">Read Image</button>
        </div>
        <div>
            Response:
            <textarea width="580" height="400"></textarea>
        </div>
        <div>
            Source Image:
            <img src="" alt="">
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        }
    }
</script>

<style lang="scss" scoped>git
    #player, #snapshot {
        width: 320px;
        height: 240px;
    }
</style>