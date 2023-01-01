<template>
  <div class="">
    <a-button type="primary" @click="showModal">Add sprints</a-button>
    <a-modal  v-model:visible="visible" title="Basic Modal" @ok="handleOk">
        <section class="flex flex-col space-y-4">
        <div>
            <a-input v-model:value="sprint" placeholder="sprint name" />
        </div>

        <div>
            <span>degree: </span>
            <a-input-number v-model:value="degree" :min="1" :max="10" />
        </div>

        <div>
            <a-select
            v-model:value="selectTasks"
            mode="multiple"
            style="width: 100%"
            placeholder="Please select Tasks Associated with this sprint"
            :options="tasks"
            @change="handleChange"
            >
        </a-select>
        </div>


        <div>
            <span>start date</span>
            <datetime v-model="startDate"
            type="datetime"
            format="YYYY-MM-DD HH:mm:ss"
            display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
            ></datetime>
        </div>

        <div>
            <span>deadline date</span>
            <datetime v-model="endDate"
            type="datetime"
            format="YYYY-MM-DD HH:mm:ss"
            display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
            ></datetime>
        </div>
        </section>


    </a-modal>
  </div>
</template>

<script>
    import { notification } from 'ant-design-vue';
import axios from 'axios';
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'

    export default {
        props: ['id'],

        data()  {
            return {
                visible: false,
                sprint: '',
                startDate: '',
                endDate: '',
                tasks: [],
                selectTasks: [],
                projectId: this.id,
                degree: ''
            }
        },
        methods: {
                handleOk(){
                    // this.visible = false;
                    console.log(this.selectTasks)

                    axios.post(`/api/v1/project/store/${this.projectId}`, 
                    {
                        tasks: this.selectTasks, 
                        sprint: this.sprint, 
                        startDate: this.startDate, 
                        endDate: this.endDate, 
                        degree: this.degree
                    })
                    .then((response)=> {
                            notification.success({
                            message: response.data.message,
                        });
                    })
                    .catch((error) => {

                        // console.log(this.$page.props)
                        notification.error({
                            message: error.response.data.message,
                        });
                    }) ;
                },
                showModal(){ 
                    this.visible  = true;
                    // console.log(this.projectId);
                    
                    this.$nextTick(() => {
                        axios.get(`/api/v1/project/index/${this.projectId}`).then((response) => {
                            // console.log(response.data);
                            const data = response.data.data;
                            this.tasks = data;       
                        });
                    })

                },
                handleChange(value, payload){
                    this.selectTasks = payload
                }
            },
        mounted(){

        },
        components: {
            'datetime': VuePersianDatetimePicker,
        }
    }
</script>