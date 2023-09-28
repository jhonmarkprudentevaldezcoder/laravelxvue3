<template>
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
        class="p-4 shadow-lg mb-12 font-bold font-sans"
    >
        <h2>TASK MANAGEMENT</h2>
    </div>
</template>
