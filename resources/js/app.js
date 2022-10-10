import "./bootstrap";
import { createApp } from "vue";

const app = createApp({});

import Index from "./components/Index.vue";
import Edit from "./components/Edit.vue";

app.component("index", Index);
app.component("edit", Edit);

app.mount("#app");
