<div id="J_prismPlayer" class="col-lg-8"></div>
<script>

    Dcat.ready(function () {
        var player = new Aliplayer({
            id: 'J_prismPlayer',
            vid: '{{$id}}',

            autoplay: false,
            playauth: '{{$playAuth}}',
            "isVBR":true,
        },function(player){
            player.play();
            console.log('The player is created.')
        });
    });
</script>

