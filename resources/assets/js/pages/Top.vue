<template>
    <div id="page">
        <h1>God Paper</h1>
        <button id="start-btn" v-on:click="start" v-if="!started">Start</button>
        <button id="capture-btn" v-on:click="capture" v-if="started">Capture</button>
        <video id="player" autoplay></video>
        <canvas id="snapshot"></canvas>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                started: false
            }
        },
        methods: {
            start() {
                document.getElementById('player');
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        player.srcObject = stream;
                    });
                this.started = true;
            },
            capture() {
                const snapshot = document.getElementById('player');
                const context = snapshot.getContext('2d');
                context.drawImage(player, 0, 0, snapshot.width, snapshot.height);
            }
        }
    }
</script>

<style lang="scss" scoped>
    #player, #snapshot {
        width: 320px;
        height: 240px;
    }
</style>