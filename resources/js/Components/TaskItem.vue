<template>
    <li class="py-3 sm:py-4">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <!-- <img class="w-8 h-8 rounded-full" src="" alt="Neil image"> -->
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                    Task Name: {{ taskTitle }}
                </p>
                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                    Task Status: {{ taskStatus ?? 'Not Set' }}
                </p>
            </div>
            <div class="inline-flex flex-col items-center text-base font-semibold text-gray-900 dark:text-white">
                       <span class="text-xs">Task Started at: {{ taskStartedAt ?? 'Not Set' }}</span> 
                       <span class="text-xs">Task Deadline at: {{ taskDeadlineAt ?? 'Not Set' }} </span>
                       <span class="text-xs"> </span>

                       <div class="flex">
                        <div class="w-full border-2 border-gray-500 p-4 rounded-lg relative">
                            <div class="absolute text-sm font-bold top-0 left-0">
                                <span>sprint</span>
                            </div>
                            <div class="flex flex-col text-xs mt-2">
                                <span>{{ taskSprint?.title ?? 'Not Set' }}</span>
                                <span>Level: {{ taskSprint?.degree ?? 'Not Set' }} </span>
                                <span>started at: {{ taskSprint?.started_at ?? 'Not Set' }}</span>
                                <span>deadline at: {{ taskSprint?.deadline_at ?? 'Not Set' }}</span>
                            </div>
                        </div>
                       </div>
            </div>
        </div>
    </li>
</template>

<script>
export default{
    props: ['title', 'started_at', 'deadline_at', 'status', 'description', 'sprint'],
    data(){
        return {
            taskTitle: this.title,
            taskStatus: this.status,
            taskStartedAt: this.started_at,
            taskDeadlineAt: this.deadline_at,
            taskDescription: this.description,
            taskSprint: this.sprint

        }
    },
    methods:{

    },

}
</script>