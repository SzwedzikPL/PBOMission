<template>
  <div class="uploader-container d-flex justify-content-center align-items-center">
    <div class="uploader-box" v-if="!uploading && !parsing">
      <div class="text-center border-bottom pb-3">
        <h3 class="m-0">A3C Reborn: Parser misji</h3>
        <span class="author text-muted">By SzwedzikPL</span>
      </div>
      <form class="mt-4">
        <div class="custom-file">
          <input type="file" class="custom-file-input" @change="onFileChange">
          <label class="custom-file-label" for="missionFile">Wybierz plik PBO</label>
        </div>
      </form>
      <div class="alert alert-danger alert-dismissible mt-3" role="alert" v-if="error">
        <span v-html="errorMessage"></span>
        <button type="button" class="close" @click="hideError">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
    <div class="uploader-progress-box text-center" v-else>
      <div class="spinner-border text-secondary" role="status"></div>
      <div class="progress" v-if="uploading">
        <div class="progress-bar" role="progressbar" :style="{width: progress+'%'}" v-text="progress+'%'"></div>
      </div>
      <div class="text-center" v-if="parsing">Parsowanie pliku misji...</div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'Uploader',
    props: {
      uploaderPath: String
    },
    data: () => ({
      uploading: false,
      progress: 0,
      parsing: false,
      error: false,
      errorMessage: ''
    }),
    methods: {
      hideError() {
        this.error = false;
      },
      onFileChange($event) {
        const fileList = $event.target.files;

        if (fileList.length) {
          this.uploadFile(fileList[0]);
          $event.target.form.reset();
        }
      },
      uploadFile(file) {
        const $this = this;
        const formData = new FormData();
        formData.append('missionFile', file);

        $this.error = false;
        $this.uploading = true;
        $this.progress = 0;

        axios.post($this.uploaderPath + 'uploader.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: progressEvent => {
            const progress = ((progressEvent.loaded / progressEvent.total)*100).toFixed(2);
            $this.progress = progress;
            if (progress >= 100) {
              $this.uploading = false;
              $this.parsing = true;
            }
          },
        }).then(response => {
          const data = response.data;

          if (!data || typeof data !== 'object' || ((!data.mission || !data.pbo) && data.error === undefined)) {
            $this.error = true;
            $this.errorMessage = 'Błąd odpowiedzi serwera. <strong>Zgłoś proszę</strong> problem administratorowi poprzez <strong>PW na forum</strong> lub <strong>TS3.</strong>';
            return;
          }

          if (data.error) {
            $this.error = true;
            $this.errorMessage = data.errorReason;
          } else {
            $this.$emit('onData', data);
          }
        }).catch(error => {
          $this.error = true;
          $this.errorMessage = 'Błąd serwera lub połączenia z nim. Spróbuj jeszcze raz lub zgłoś problem administratorowi jeśli problem się powtarza.';
        }).finally(() => {
          $this.progress = 0;
          $this.uploading = false;
          $this.parsing = false;
        });
      }
    }
  }
</script>

<style scoped>
  .uploader-container {
    min-height: 100vh;
  }
  .uploader-box {
    min-width: 40vw;
  }
  .uploader-progress-box {
    min-width: 400px;
  }
  .uploader-progress-box > .spinner-border {
    margin-bottom: 10px;
  }
  .uploader-progress-box > .progress {
    height: 20px;
  }

  .author {
    font-size: 12px;
    opacity: 0.6;
  }
</style>
