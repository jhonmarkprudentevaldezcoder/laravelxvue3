<script>
import TaskTable from "./TaskTable.vue";
import Nav from "./Nav.vue";
import HeaderSection from "./HeaderSection.vue";
</script>

<template>
    <div class="bg-gray-50 p-3 sm:p-5">
        <HeaderSection />
        <div class="mx-auto max-w-screen px-1 lg:px-3 shadow-lg">
            <div
                class="bg-white relative shadow-md sm:rounded-lg overflow-hidden"
            >
                <TaskTable />
                <Nav />
            </div>
        </div>
    </div>
</template>
