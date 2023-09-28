import "./bootstrap";
import "flowbite";

import { createApp } from "vue";
import Tasks from "./components/Tasks.vue";
import TaskTable from "./components/TaskTable.vue";
import Nav from "./components/Nav.vue";
import HeaderSection from "./components/HeaderSection.vue";
import { MotionPlugin } from "@vueuse/motion";

const app = createApp();

app.use(MotionPlugin);

app.component("Tasks", Tasks);
app.component("TaskTable", TaskTable);
app.component("Nav", Nav);
app.component("HeaderSection", HeaderSection);

app.mount("#app");
