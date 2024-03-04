<template>
    <div>
      <video-player :options="videoOptions" class="video" />
    </div>
</template>

<style scoped>
    .video{
        width: 500px;
        height: 500px;
    }
</style>

  <script>
  import VideoPlayer from '@/components/VideoPlayer.vue';
  import 'video.js/dist/video-js.css';
  export default {
    name: 'VideoExample',
    components: {
      VideoPlayer
    },
    data() {
        return {
                videoOptions: {
                    sources: [{
                    src: 'http://localhost:8000/storage/videos/experiment.mp4', // Replace with your video URL
                    type: 'video/mp4',
                    }],
                    controls: true, // Show player controls
                    autoplay: false, // Autoplay the video
                    preload: 'auto', // Preload the video metadata
                    loop: false, // Loop the video playback
                    muted: false, // Mute the audio
                    poster: 'http://localhost:8000/storage/photos/ZB35i5FAeSNJP4LfOskTYkRkHvAm2qt4HAhheUXy.jpg', // Set a poster image for the video
                    fluid: true, // Enable fluid mode for responsive design
                    playbackRates: [0.5, 1, 1.5, 2], // Set available playback rates
                    responsive: true, // Make the player responsive
                    aspectRatio: '16:9', // Set the aspect ratio
                    // Add more options as needed
                },
            };
        },
  };
  </script>
