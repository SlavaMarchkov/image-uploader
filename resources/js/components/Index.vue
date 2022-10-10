<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">DropZone</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input
                                v-model="title"
                                type="text"
                                class="form-control"
                                placeholder="Post Title"
                            />
                        </div>
                        <div class="mb-3">
                            <vue-editor
                                v-model="content"
                                useCustomImageHandler
                                @image-added="handleImageAdded"
                            />
                        </div>
                        <div class="mb-3">
                            <button
                                ref="dropzone"
                                class="btn w-100 p-5 btn-dark text-center"
                                @click.prevent
                            >
                                Upload here
                            </button>
                        </div>
                        <div class="mb-3">
                            <button
                                @click.prevent="store"
                                type="submit"
                                class="btn btn-primary"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="post">
            <h4>{{ post.title }}</h4>
            <div class="mb-3">
                <a :href="edit_link" class="btn btn-danger"
                    >Edit</a
                >
            </div>
            <div v-for="image in post.images" :key="image" class="mb-2">
                <div class="mb-2">
                    <img :src="image.preview_url" alt="#" />
                </div>
                <div>
                    <img :src="image.url" alt="#" class="w-50" />
                </div>
            </div>
            <div v-html="post.content" class="ql-editor"></div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import DropZone from "dropzone";
import { VueEditor } from "vue3-editor";

export default {
    data() {
        return {
            dropzone: null,
            title: null,
            post: null,
            edit_link: "",
            content: "",
        };
    },

    components: { VueEditor },

    mounted() {
        this.dropzone = new DropZone(this.$refs.dropzone, {
            clickable: true,
            autoProcessQueue: false,
            addRemoveLinks: true,
            url: "/api/posts",
        });
        this.getPost();
    },

    methods: {
        store() {
            const data = new FormData();
            const files = this.dropzone.getAcceptedFiles();
            files.forEach((file) => {
                data.append("images[]", file);
                this.dropzone.removeFile(file);
            });
            data.append("title", this.title);
            data.append("content", this.content);
            this.title = "";
            this.content = "";
            axios.post("/api/posts", data).then(() => {
                this.getPost();
            });
        },

        getPost() {
            axios.get("/api/posts").then((response) => {
                this.post = response.data.data;
                this.edit_link = '/edit/' + this.post.id;
            });
        },

        handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            const formData = new FormData();
            formData.append("image", file);

            axios
                .post("/api/posts/images", formData)
                .then((result) => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch((err) => {
                    console.log(err);
                });
        },
    },
};
</script>

<style>
.dz-success-mark,
.dz-error-mark {
    display: none;
}
.ql-editor img {
    max-width: 100%;
}
</style>
