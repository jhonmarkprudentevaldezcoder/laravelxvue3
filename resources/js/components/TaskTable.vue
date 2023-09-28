<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { useToastr } from "../../js/toastr.js";

const toastr = useToastr();
const tasks = ref([]);
const showEditModal = ref(false);
const showAddModal = ref(false);
const showDeleteModal = ref(false);
const searchQuery = ref(null);
const taskCount = ref(0);
let deleteTaSk = ref(null);

const searchTasks = ref({
    id: "",
    name: "",
    description: "",
    category: "",
    person: "",
    position: "",
});

const newTask = ref({
    name: "",
    description: "",
    category: "",
    person: "",
    position: "",
});

onMounted(() => {
    fetctAllTask();
    filterTask();
});

const fetctAllTask = async () => {
    try {
        const response = await axios.get("api/tasks");
        tasks.value = response.data;
        taskCount.value = response.data.length;
    } catch (error) {
        console.error("Error fetching tasks:", error);
    }
};

const addTask = async () => {
    try {
        const response = await axios.post("api/tasks/store", newTask.value);

        if (response.status === 201) {
            console.log("Task added successfully");
            newTask.value = {
                name: "",
                description: "",
                category: "",
                person: "",
                position: "",
            };
            updateAddModal();

            toastr.success("Task addedsuccess");
            fetctAllTask();
        } else {
            console.error("Failed to add task. Status code:", response.status);
        }
    } catch (error) {
        console.error("Error adding task:", error);
    }
};

const updateAddModal = () => {
    showAddModal.value = !showAddModal.value;
};

const updateDeleteModal = () => {
    showDeleteModal.value = !showDeleteModal.value;
};

const updateEditNav = () => {
    showEditModal.value = !showEditModal.value;
};

const saveDeleteTask = async (deleteTaSk) => {
    try {
        const response = await axios.delete(`/api/tasks/${deleteTaSk}`);

        if (response.status === 200) {
            console.log("Task deleted successfully");

            fetctAllTask();
            toastr.success("Task Deleted success");
            updateDeleteModal();
        } else {
            console.error(
                "Failed to delete task. Status code:",
                response.status
            );
        }
    } catch (error) {
        console.error("Error deleting task:", error);
    }
};

const deleteTask = (taskId) => {
    updateDeleteModal();

    deleteTaSk.value = taskId;
    console.log(deleteTaSk);
};

const searchTask = async (id) => {
    updateEditNav();
    try {
        const response = await axios.get(`api/tasks/task/${id}`);
        searchTasks.value = response.data.task;
        searchTasks.id = searchTasks.value.id;
        console.log(searchTasks.id);
    } catch (error) {
        console.error("Error fetching tasks:", error);
    }
};

const updaeteTask = async () => {
    try {
        const response = await axios.put(
            `api/tasks/${searchTasks.id}`,
            searchTasks.value
        );

        if (response.status >= 200 && response.status < 300) {
            console.log("Task Updated successfully");

            fetctAllTask();
            updateEditNav();
            toastr.success("Task Updated success");
        } else {
            console.error(
                "Failed to Updated task. Status code:",
                response.status
            );
        }
    } catch (error) {
        console.error("Error Updating task:", error);
    }
};

const updaeteCompletedTask = async (id) => {
    try {
        const response = await axios.put(
            `api/tasks/completed/${id}`,
            tasks.value
        );

        if (response.status >= 200 && response.status < 300) {
            console.log("Task Completed successfully");

            fetctAllTask();

            toastr.success("Task Completed success");
        } else {
            console.error(
                "Failed to Complete task. Status code:",
                response.status
            );
        }
    } catch (error) {
        console.error("Error Completing task:", error);
    }
};

const inCompletedTask = async (id) => {
    try {
        const response = await axios.put(
            `api/tasks/incompleted/${id}`,
            tasks.value
        );

        if (response.status >= 200 && response.status < 300) {
            console.log("Task Completed successfully");

            fetctAllTask();

            toastr.error("Task In Completed ");
        } else {
            console.error(
                "Failed to Complete task. Status code:",
                response.status
            );
        }
    } catch (error) {
        console.error("Error Completing task:", error);
    }
};

const filterTask = () => {
    axios
        .get(`api/tasks/search`, {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            tasks.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

watch(searchQuery, () => {
    filterTask();
});
</script>
<template>
    <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
    >
        <div class="flex-1 flex items-center space-x-2">
            <h5>
                <span class="text-gray-500"> tasks </span>
            </h5>
            <h5 class="text-gray-500 ml-1">( {{ taskCount }} )</h5>

            <div
                id="results-tooltip"
                role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip"
            >
                Showing 1-100 of 436 results
                <div class="tooltip-arrow" data-popper-arrow=""></div>
            </div>
        </div>
    </div>
    <div
        class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t"
    >
        <div class="w-full md:w-1/2">
            <div class="flex items-center">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full flex flex-row gap-2">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                    >
                        <svg
                            aria-hidden="true"
                            class="w-5 h-5 text-gray-500"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            />
                        </svg>
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        id="simple-search"
                        placeholder="Search for task"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2"
                    />
                </div>
            </div>
        </div>
        <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        >
            <button
                @click="updateAddModal"
                type="submit"
                class="flex items-center justify-center text-white bg-green-600 bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2"
            >
                <svg
                    class="h-3.5 w-3.5 mr-1.5 -ml-1"
                    fill="currentColor"
                    viewbox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        clip-rule="evenodd"
                        fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    />
                </svg>
                Add Task
            </button>
        </div>
    </div>

    <div class="overflow-x-auto relative">
        <div
            v-motion
            :initial="{
                opacity: 0,
                y: 100,
            }"
            :enter="{
                opacity: 1,
                y: 0,
            }"
            v-if="tasks.length == 0"
            class="w-full bg-gray-400 p-2 text-white font-sans font-bold text-sm"
        >
            <p>No Task found</p>
        </div>

        <table v-else class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr
                    v-motion
                    :initial="{
                        opacity: 0,
                        y: 100,
                    }"
                    :enter="{
                        opacity: 1,
                        y: 0,
                    }"
                >
                    <th scope="col" class="p-4">TASK</th>
                    <th scope="col" class="p-4">TASK WORKER</th>
                    <th scope="col" class="p-4">TASK DESCRIPTION</th>
                    <th scope="col" class="p-4">CATEGORY</th>
                    <th scope="col" class="p-4">CREATED AT</th>
                    <th scope="col" class="p-4">COMPLETED AT</th>
                    <th scope="col" class="p-4">STATUS</th>
                    <th scope="col" class="p-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-motion
                    :initial="{
                        opacity: 0,
                        y: 100,
                    }"
                    :enter="{
                        opacity: 1,
                        y: 0,
                    }"
                    v-for="(task, index) in tasks"
                    :key="task.id"
                    class="border-b hover:bg-gray-100 transition-all ease-in-out duration-75"
                >
                    <th
                        scope="row"
                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ task.name }}
                    </th>

                    <th
                        scope="row"
                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ task.person }}
                    </th>
                    <td class="px-4 py-3">
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded"
                        >
                            {{ task.description }}</span
                        >
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded"
                        >
                            {{ task.category }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded"
                        >
                            {{ task.created_at }}</span
                        >
                    </td>
                    <td class="px-4 py-3">
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded"
                        >
                            {{
                                task.completed_at == null
                                    ? "Not Completed"
                                    : task.completed_at
                            }}</span
                        >
                    </td>
                    <td
                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap"
                    >
                        <div class="flex items-center">
                            <div
                                :class="{
                                    'bg-red-900 w-2 h-2 ': !task.completed,
                                    'bg-green-600 w-2 h-2': task.completed,
                                    'animate-ping':
                                        task.category === 'Urgent' &&
                                        !task.completed,
                                    'animate-bounce':
                                        task.category === 'Important' &&
                                        !task.completed,
                                }"
                                class="h-4 w-4 rounded-full inline-block mr-2"
                            ></div>
                            {{ task.completed ? "Completed" : "Not Completed" }}
                        </div>
                    </td>

                    <td
                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap"
                    >
                        <div class="flex items-center space-x-4">
                            <button
                                @click="searchTask(task.id)"
                                type="button"
                                class="py-2 px-3 flex items-center text-sm bg-slate-800 font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-slate-900 focus:ring-4 focus:outline-none focus:ring-primary-300"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-2 -ml-0.5"
                                    viewbox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Edit
                            </button>
                            <div v-if="task.completed">
                                <button
                                    @click="inCompletedTask(task.id)"
                                    type="button"
                                    class="py-2 px-3 flex items-center text-sm font-medium text-center text-white focus:outline-none bg-gray-800 rounded-lg border border-gray-200 hover:bg-green-900 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewbox="0 0 24 24"
                                        fill="currentColor"
                                        class="w-4 h-4 mr-2 -ml-0.5"
                                    >
                                        <path
                                            d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                                        />
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"
                                        />
                                    </svg>
                                    Incompleted
                                </button>
                            </div>
                            <div v-else>
                                <button
                                    @click="updaeteCompletedTask(task.id)"
                                    type="button"
                                    class="py-2 px-3 flex items-center text-sm font-medium text-center text-white focus:outline-none bg-green-800 rounded-lg border border-gray-200 hover:bg-green-900 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewbox="0 0 24 24"
                                        fill="currentColor"
                                        class="w-4 h-4 mr-2 -ml-0.5"
                                    >
                                        <path
                                            d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                                        />
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"
                                        />
                                    </svg>
                                    Complete
                                </button>
                            </div>

                            <button
                                @click="deleteTask(task.id)"
                                class="flex items-center text-white border border-red-700 bg-red-900 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-2 -ml-0.5"
                                    viewbox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div
        :class="showEditModal ? 'block' : 'hidden'"
        class="fixed z-auto overflow-y-auto overflow-x-hidden top-0 right-0 justify-center items-center w-full h-screen md:h-screen"
    >
        <div class="relative p-4 w-full max-w-3xl h-full md:h-screen">
            <div
                class="relative h-[calc(100%-1rem)] p-4 bg-gray-200 rounded-lg shadow"
            >
                <div
                    class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Edit Task
                    </h3>
                    <button
                        @click="updateEditNav"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form @submit.prevent="updaeteTask">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Task Name</label
                            >
                            <input
                                type="text"
                                name="name"
                                v-model="searchTasks.name"
                                id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Type Task name"
                                required
                            />
                        </div>
                        <div>
                            <label
                                for="category"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Category</label
                            ><select
                                v-model="searchTasks.category"
                                id="category"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                            >
                                <option value="" disabled selected>
                                    Select category
                                </option>

                                <option value="Urgent">Urgent</option>
                                <option value="Important">Important</option>
                                <option value="Not Urgent but Important">
                                    Not Urgent but Important
                                </option>
                                <option value="Urgent but Not Important">
                                    Urgent but Not Important
                                </option>
                                <option value="Not Urgent and Not Important">
                                    Not Urgent and Not Important.
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="position"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >position</label
                            ><select
                                v-model="searchTasks.position"
                                id="position"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                            >
                                <option value="" disabled selected>
                                    Select position
                                </option>
                                <option value="Front end">Front end</option>
                                <option value="Back end">Back end</option>
                                <option value="UI/UX">UI/UX</option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="person"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Person</label
                            >
                            <input
                                type="text"
                                v-model="searchTasks.person"
                                name="person"
                                id="person"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Person name"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Description</label
                            ><textarea
                                id="description"
                                v-model="searchTasks.description"
                                rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Write Task description here"
                            ></textarea>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="justify-center w-full text-white inline-flex items-center bg-green-600 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 focus:z-10"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-2 -ml-0.5"
                            viewbox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                            />
                            <path
                                fill-rule="evenodd"
                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        UPDATE
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- add modal -->
    <div
        :class="showAddModal ? 'block' : 'hidden'"
        class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] md:h-full"
    >
        <div class="relative p-4 w-full max-w-3xl h-full md:h-screen 0">
            <div
                class="relative p-4 h-[calc(100%-1rem)] bg-gray-200 rounded-lg shadow sm:p-5"
            >
                <div
                    class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Add Task
                    </h3>
                    <button
                        @click="updateAddModal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center e"
                    >
                        <svg
                            aria-hidden="true"
                            class="w-5 h-5"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form @submit.prevent="addTask">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Task Name</label
                            >
                            <input
                                v-model="newTask.name"
                                type="text"
                                name="name"
                                id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Type Task name"
                                required
                            />
                        </div>
                        <div>
                            <label
                                for="category"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Category</label
                            ><select
                                id="category"
                                v-model="newTask.category"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                            >
                                <option value="" disabled selected>
                                    Select category
                                </option>

                                <option value="Urgent">Urgent</option>
                                <option value="Important">Important</option>
                                <option value="Not Urgent but Important">
                                    Not Urgent but Important
                                </option>
                                <option value="Urgent but Not Important">
                                    Urgent but Not Important
                                </option>
                                <option value="Not Urgent and Not Important">
                                    Not Urgent and Not Important.
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="position"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >position</label
                            ><select
                                v-model="newTask.position"
                                id="position"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                            >
                                <option value="" disabled selected>
                                    Select position
                                </option>
                                <option value="Front end">Front end</option>
                                <option value="Back end">Back end</option>
                                <option value="UI/UX    ">UI/UX</option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="person"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Person</label
                            >
                            <input
                                v-model="newTask.person"
                                type="text"
                                name="person"
                                id="person"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Person name"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Description</label
                            ><textarea
                                id="description"
                                rows="4"
                                v-model="newTask.description"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Write Task description here"
                            ></textarea>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="justify-center w-full text-white inline-flex items-center bg-green-600 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        <svg
                            class="h-3.5 w-3.5 mr-1.5 -ml-1"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            />
                        </svg>
                        ADD
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- delete modal -->
    <div
        :class="showDeleteModal ? 'block' : 'hidden'"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="bg-white w-full md:w-1/3 p-6 rounded-lg shadow-lg">
            <p class="text-lg font-semibold mb-4">
                Are you sure you want to delete this task?
            </p>
            <div class="flex justify-end">
                <button
                    @click="saveDeleteTask(deleteTaSk)"
                    class="bg-red-500 text-white px-4 py-2 rounded-md mr-2"
                >
                    Yes
                </button>
                <button
                    @click="updateDeleteModal"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md"
                >
                    No
                </button>
            </div>
        </div>
    </div>
</template>
