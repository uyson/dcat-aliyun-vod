
<div class="dcat-box" id="vod-app">
    <div class="d-block pb-0">
        <div class="custom-data-table-header">
            <div class="table-responsive">
                <div class="top d-block clearfix p-0">
                    <button class="btn btn-primary btn-mini btn-outline" data-toggle="modal" data-target="#add-vod-files" style="margin-right:3px">上传</button>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive table-wrapper complex-container table-middle mt-1 table-collapse ">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">视频名称</th>
                <th scope="col">格式</th>
                <th scope="col">大小</th>
                <th scope="col">分类</th>
                <th scope="col">转码组</th>
                <th scope="col">上传状态</th>
                <th scope="col">操作</th>
            </tr>
            </thead>
            <tbody>
                <tr

                    is="upload-item"
                    v-for="(item, index) in taskList"
                    :key="index"
                    :file="item.file"
                    :file-name="item.fileName"
                    :cate-name="item.cateName"
                    :template-group-name="item.templateGroupName"
                    :user-data="item.userData"
                    :user-id="userId"
                    :region="region"
                    :create-upload-video-auth-url="createUploadVideoAuthUrl"
                    :refresh-upload-video-auth-url="refreshUploadVideoAuthUrl"
                ></tr>
                <tr v-if="taskList.length === 0">
                    <td colspan="7">请添加视频</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="add-vod-files">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group">
                                <label class="btn btn-primary btn-mini" for="select-vod-files">
                                    添加视频
                                </label>
                                <input
                                    accept="video/mp4"
                                    multiple
                                    v-on:change="filesChange($event.target.name, $event.target.files)"
                                    id="select-vod-files"
                                    type="file"
                                    style="width: 0px; opacity: 0; display:inline-block;" />
                            </div>

                            <div class="form-group">
                                <select class="form-control" v-model="currentTranscode">
                                    <option v-for="(text, key) in transcodes" v-bind:value="key">@{{ text }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" v-model="currentCategory">
                                    <option v-for="(text, key) in categories" v-bind:value="key">@{{ text }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">音/视频名称</th>
                                    <th scope="col">格式</th>
                                    <th scope="col">大小</th>
                                    <th scope="col">分类</th>
                                    <th scope="col">转码模板组</th>
                                    <th scope="col">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(file, index) in files">
                                    <th scope="row">
                                        <input type="text" v-model="file.filename">
                                    </th>
                                    <td>@{{ file.file.type }}</td>
                                    <td>@{{ renderSize(file.file.size) }}</td>
                                    <td>@{{ categories[file.category] }}</td>
                                    <td>@{{ transcodes[file.transcode] }}</td>
                                    <td><span class="badge badge-danger" v-on:click="removeFileByIndex(index)">删除</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" v-on:click="addFilesToTaskList()">添加</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script require="@uyson.dcat-aliyun-vod">
    var app = new Vue({
        el: '#vod-app',
        data: {
            transcodes: @json($transcodes),
            categories: @json($categories),
            currentTranscode: null,
            currentCategory: null,
            files : [],
            taskList : [],
            userId: "{{$userId}}",
            region: "{{$region}}",
            refreshUploadVideoAuthUrl: "{{admin_url('dcat-aliyun-vod/videos/refresh-upload-video-request')}}",
            createUploadVideoAuthUrl : "{{admin_url('dcat-aliyun-vod/videos/create-upload-video-request')}}",
        },
        created() {
            this.currentTranscode = Object.keys(this.transcodes)[0] ?? null;
            this.currentCategory = Object.keys(this.categories)[0] ?? null;


        },
        methods: {
            renderSize(value){
                if(null==value||value==''){
                    return "0 Bytes";
                }
                var unitArr = new Array("Bytes","KB","MB","GB","TB","PB","EB","ZB","YB");
                var index=0;
                var srcsize = parseFloat(value);
                index=Math.floor(Math.log(srcsize)/Math.log(1024));
                var size =srcsize/Math.pow(1024,index);
                size=size.toFixed(2);//保留的小数位数
                return size+unitArr[index];
            },
            removeFileByIndex(index) {
                this.files.splice(index, 1);
            },
            filesChange (fieldName, fileList) {
                for(var i = 0; i < fileList.length; i++) {
                        this.files.push({
                            file: fileList.item(i),
                            transcode : this.currentTranscode,
                            category: this.currentCategory,
                            filename: fileList.item(i).name,
                        })
                }
                // console.warn('files', this.files)
            },
            addFilesToTaskList() {
                //['file', 'fileName', 'cateName', 'templateGroupName', 'userData']
                this.files.map(file=> {
                    let paramsData = {
                        Vod: {
                            Title: file.filename ,
                            CateId: file.category,
                            TemplateGroupId: file.transcode,
                        }
                    };
                    let item = {
                        file: file.file,
                        fileName: file.filename,
                        cateName: this.categories[file.category],
                        templateGroupName: this.transcodes[file.transcode],
                        userData: paramsData,
                    };
                    // console.log('item', item)
                    this.taskList.push(item)
                });
                this.files = [];
                $('#add-vod-files').modal('hide');
            },
        },
    });

</script>
