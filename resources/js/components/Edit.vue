<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit</div>
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
                                @image-removed="handleImageRemoved"
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
                                @click.prevent="update"
                                type="submit"
                                class="btn btn-success"
                            >
                                Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
            postId: "",
            content: "",
            imageIdsForDelete: [],
            imageUrlsForDelete: [],
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

        this.dropzone.on("removedfile", (file) => {
            this.imageIdsForDelete.push(file.id);
        });

        this.getPost();
    },

    methods: {
        update() {
            const data = new FormData();
            const files = this.dropzone.getAcceptedFiles();
            files.forEach((file) => {
                data.append("images[]", file);
                this.dropzone.removeFile(file);
            });

            this.imageIdsForDelete.forEach((idForDelete) => {
                data.append("image_ids_for_delete[]", idForDelete);
            });

            this.imageUrlsForDelete.forEach((urlForDelete) => {
                data.append("image_urls_for_delete[]", urlForDelete);
            });

            data.append("title", this.title);
            data.append("content", this.content);
            data.append("_method", "PATCH");
            this.title = "";
            this.content = "";
            axios.post("/api/posts/" + this.postId, data).then(() => {
                const previews =
                    this.dropzone.previewsContainer.querySelectorAll(
                        ".dz-image-preview"
                    );

                previews.forEach((preview) => {
                    preview.remove();
                });

                this.getPost();
            });
        },

        getPost() {
            const uri = window.location.href.split("/");
            this.postId = parseInt(uri.pop());
            axios.get("/api/posts/" + this.postId).then((response) => {
                const post = response.data.data;
                this.title = post.title;
                this.content = post.content;
                post.images.forEach((image) => {
                    const file = {
                        id: image.id,
                        name: image.name,
                        size: image.size,
                    };
                    this.dropzone.displayExistingFile(file, image.preview_url);
                });
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

        handleImageRemoved(url) {
            this.imageUrlsForDelete.push(url);
        },
    },
};
</script>

<style></style>
