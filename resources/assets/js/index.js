Vue.component('upload-item', {
    props: ['file', 'fileName', 'cateName', 'templateGroupName', 'userData', 'region', 'userId',
        'refreshUploadVideoAuthUrl', 'createUploadVideoAuthUrl'],
    data: function () {
        return {
            uploadDisabled: true,
            resumeDisabled: true,
            pauseDisabled: true,
            authProgress: 0,
            statusText: '',
            uploader: null,
        };
    },
    created : function() {
        this.uploader = this.createUploader();
        this.uploader.addFile(this.file,null, null, null, JSON.stringify(this.userData));
        this.uploadDisabled = false;
        this.pauseDisabled = true;
        this.resumeDisabled = true;
    },
    methods: {
        createUploader: function(){
            var self = this;
            var uploader = new AliyunUpload.Vod({
                userId: self.userId,
                region: self.region,
                partSize: 1048576,
                parallel: 5,
                retryCount: 3,
                retryDuration: 2,
                //开始上传
                'onUploadstarted':  function (uploadInfo) {
                    // console.warn('onUploadstarted', uploadInfo, uploadInfo.state);
                    if(!uploadInfo.videoId)//这个文件没有上传异常
                    {
                        fetch(
                            self.createUploadVideoAuthUrl,
                            {
                                'method': 'POST',
                                'body': JSON.stringify({
                                    'filename': uploadInfo.file.name,
                                    'title' : uploadInfo.videoInfo.Title,
                                    'cateId': uploadInfo.videoInfo.CateId,
                                    'templateGroupId': uploadInfo.videoInfo.TemplateGroupId,
                                }),
                                headers: new Headers({
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': Dcat.token,
                                })
                            }
                        ).then( function(res) {return res.json()})
                            .catch( function (error){
                                console.error('error', error);
                            })
                            .then(function(res) {
                                uploader.setUploadAuthAndAddress(uploadInfo, res.UploadAuth, res.UploadAddress,res.VideoId);
                                self.statusText = '开始上传...'
                            });

                    }
                    else//如果videoId有值，根据videoId刷新上传凭证
                    {
                        fetch(
                            self.refreshUploadVideoAuthUrl,
                            {
                                'method': 'POST',
                                'body': JSON.stringify({
                                    'videoId': uploadInfo.videoId,
                                }),
                                headers: new Headers({
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': Dcat.token,
                                })
                            }
                        ).then(function (res) {
                            return res.json();
                        })
                            .catch(function (error) {
                                console.error('error', error);
                            })
                            .then(function (res) {
                                uploader.setUploadAuthAndAddress(uploadInfo, res.UploadAuth, res.UploadAddress);
                            });

                    }
                },
                //文件上传成功
                'onUploadSucceed': function (uploadInfo) {
                    // console.warn('onUploadSucceed', uploadInfo, uploadInfo.state);
                    self.statusText = '上传成功!';

                },
                //文件上传失败
                'onUploadFailed': function (uploadInfo, code, message) {
                    // console.warn('onUploadFailed', uploadInfo, code, message, uploadInfo.state)
                    self.statusText = '上传失败!';
                },
                //文件上传进度，单位：字节
                'onUploadProgress': function (uploadInfo, totalSize, loadedPercent) {
                    // console.log('uploadInfo', JSON.stringify(uploadInfo));
                    // console.log('onUploadProgress', uploadInfo, totalSize, loadedPercent, uploadInfo.state)
                    let progressPercent = Math.ceil(loadedPercent * 100);
                    self.authProgress = progressPercent;
                    self.statusText = '上传中...';
                },
                //上传凭证或STS token超时
                'onUploadTokenExpired': function (uploadInfo) {
                    console.warn('onUploadTokenExpired', uploadInfo, uploadInfo.state);
                    fetch(
                        self.refreshUploadVideoAuthUrl,
                        {
                            'method': 'POST',
                            'body': JSON.stringify({
                                'videoId': uploadInfo.videoId,
                            }),
                            headers: new Headers({
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{csrf_token()}}",
                            })
                        }
                    ).then(res => res.json())
                        .catch(error => {
                            console.error('error', error);
                        })
                        .then(res => {
                            uploader.resumeUploadWithAuth(res.UploadAuth);
                            console.log('upload expired and resume upload with uploadauth ' + res.UploadAuth)
                        })
                    self.statusText = '文件超时...';
                },
                //全部文件上传结束
                'onUploadEnd':function(uploadInfo){
                    // console.warn('onUploadEnd', uploadInfo, uploadInfo.state)
                    self.statusText = '上传完毕'
                }
            });
            return uploader;
        },
        authUpload () {
            // 然后调用 startUpload 方法, 开始上传
            if (this.uploader !== null) {
                this.uploader.startUpload();
                this.uploadDisabled = true;
                this.pauseDisabled = false;
            }
        },
        // 暂停上传
        pauseUpload () {
            if (this.uploader !== null) {
                this.uploader.stopUpload();
                this.resumeDisabled = false;
                this.pauseDisabled = true;
            }
        },
        // 恢复上传
        resumeUpload () {
            if (this.uploader !== null) {
                this.uploader.startUpload();
                this.resumeDisabled = true;
                this.pauseDisabled = false;
            }
        },
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
    },
    template: `<tr>
        <td>{{fileName}} </td>
        <td>{{ renderSize(file.size) }} </td>
        <td>{{ file.type }} </td>
        <td>{{ cateName }} </td>
        <td>{{ templateGroupName}} </td>
        <td>{{ statusText }} {{ authProgress }}% </td>
        <td>
            <button @click="authUpload" v-bind:disabled="uploadDisabled">开始上传</button>
            <button @click="pauseUpload" v-bind:disabled="pauseDisabled">暂停</button>
            <button v-bind:disabled="resumeDisabled" @click="resumeUpload">恢复上传</button>
        </td>
    </tr>`
});
