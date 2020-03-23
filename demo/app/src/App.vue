<template>
  <div id="app" class="container-fluid">
    <DataViewer :data="data" @reset="resetData" v-if="data" />
    <Uploader :uploaderPath="uploaderPath" @onData="onData" v-else />
  </div>
</template>

<script>
import Uploader from './components/Uploader.vue';
import DataViewer from './components/DataViewer.vue';

export default {
  name: 'App',
  data: () => ({
    uploaderPath: process.env.VUE_APP_UPLOADER_PATH,
    data: window.mission ? window.mission : null
  }),
  methods: {
    resetData() {
      window.history.pushState({}, document.title, window.location.href.split('?')[0]);
      this.data = null;
    },
    onData(data) {
      this.data = data;
    }
  },
  components: {Uploader, DataViewer}
}
</script>

<style>

</style>
