<template>
    <a-button @click="edit(id)" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="update(id)">
        <section class="flex gap-1">
            <div class="w-1/2">
                <a-form layout="vertical" name="form_in_modal">
                    <a-form-item name="task name" label="Title" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                        <a-input v-model:value="taskName" />
                    </a-form-item>

                    <a-form-item name="task name" label="description" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                            <a-textarea v-model:value="taskDescription" placeholder="Description" :rows="4" />
                    </a-form-item>

                    <a-select
                        ref="select"
                        v-model:value="status"
                        style="width: 120px"
                        @change="selectStatus"
                        >
                        <a-select-option value="Active">Active</a-select-option>
                        <a-select-option value="Suspended">Suspended</a-select-option>
                        <a-select-option value="Completed">Completed</a-select-option>
                    </a-select>

                    <div dir="rtl" class="text-left">
                        <label for="">
                            Deadline date
                        </label>
                        <datetime v-model="deadline"
                         type="datetime"
                         format="YYYY-MM-DD HH:mm:ss"
                         display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
                         ></datetime>
                    </div>
                    

                </a-form>
            </div>

            <div class="w-1/2">
                <div class="mr-1 mb-2"><span class="text-red-500">*</span> Tasks:</div>
                
                  <a-select
                    v-model:value="value"
                    mode="multiple"
                    style="width: 100%"
                    placeholder="Please select tasks"
                    :options="tasks"
                    @change="handleChange"
                ></a-select>

                <user-task v-model="selectedUsers" :taskId="id" @user-selected="userSelected"></user-task>
                

                <template v-if="!started_at">
                    <caret-right-outlined @click="start(id)" :style="{fontSize: '100px', color: '#08c', cursor: 'pointer'}"/>
                    <div class="flex"><span>در تاریخ <span v-text="convertDate(paused_at)"></span> متوقف شده است</span>   </div>
                </template>
                <template v-else>
                    <pause-outlined @click="pause(id)" :style="{fontSize: '100px', color: 'yellow', cursor: 'pointer'}"/>
                    <div class="flex"><span>در تاریخ <span v-text="convertDate(started_at)"></span> آغاز شده است</span>   </div>
                </template>
            </div>
        </section>

    </a-modal>
    
</template>

<script>
import { EditOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import DynamicInput from './DynamicInput.vue';
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'
import { CaretRightOutlined, PauseOutlined } from '@ant-design/icons-vue';
import UserTask from './UserTask.vue'

export default {
    props: ["id", "name"],
    // emits: ['changeRoleName'],
    data() {
        return {
            value: [],
            selectedUsers: [],
            visible: false,
            taskName: '',
            taskDescription: '',
            tasks: [{id: '', value: ''}],
            deadline: '',
            status: null,
            // taskStarted: null,
            started_at: '',
            paused_at: '',
        }
    },

    methods: {

        edit(taskId) {
            this.visible = true;
            axios
                .get(route("task.show", { task: taskId }))
                .then((response) => {
                    const data = response.data.data;
                    console.log(data)
                    this.taskDescription = data.description;
                    this.taskName = data.title;
                    this.deadline = data.deadline_at;
                    this.status = data.status;
                    this.tasks = data.tasks;
                    // this.taskStarted = data.taskStarted;
                    this.started_at = data.started_at;
                    this.paused_at = data.paused_at;
                    this.value = data.value
                });
        },
        update(taskId){
            console.log(this)
            axios.put(route('task.update', {task: taskId}), {
                value: this.value,
                taskDescription: this.taskDescription,
                taskName: this.taskName,
                started_at: this.started_at.length ? moment(this.started_at).format('YYYY-M-D HH:mm:ss') : '',
                paused_at: this.paused_at.length ? moment(this.paused_at).format('YYYY-M-D HH:mm:ss') : '',
                status: this.status,
                deadline: this.deadline,
                users: this.selectedUsers

            }).then(response => {
                notification.success({
                        message: response.data.message,
                    });
            }).catch(error => {
                notification.error({
                    message: error.response.data.message,
                });
            })
        },


        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
        handleChange(value, payload){
            this.value = payload
        },
        userSelected(event){
            this.selectedUsers = event;
            console.log(this.selectedUsers);
        },
        start(taskId){
            axios.get(route('task.start', {task: taskId})).then(response => {
                this.taskStarted = response.data.taskStarted
                this.started_at = response.data.date;
                this.paused_at = ''
            });
        },
        pause(taskId){
            axios.get(route('task.stop', {task: taskId})).then(response => {
                this.taskStarted = response.data.taskStarted;
                this.paused_at = response.data.date;
                this.started_at = ''
            });
        },
        selectStatus(value){
            this.status = value;
        },
        convertDate(date){
            return date ? moment(date).format('jYYYY/jM/jD h:m:s') : '';
        }
    },
    mounted(){console.log('yes')},
    components: {
        EditOutlined, 
        DynamicInput,
        'datetime': VuePersianDatetimePicker,
        CaretRightOutlined,
        PauseOutlined,
        UserTask
    },
};
</script>