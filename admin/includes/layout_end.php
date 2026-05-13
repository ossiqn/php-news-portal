</div>

<footer style="background:var(--c1);color:rgba(255,255,255,.3);text-align:center;padding:12px 20px;font-size:11px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
    <span>Yonetim Paneli v<?= ADMIN_VERSION ?></span>
    <span>PHP <?= PHP_MAJOR_VERSION ?>.<?= PHP_MINOR_VERSION ?> &nbsp;&middot;&nbsp; <?= date('d.m.Y H:i') ?></span>
</footer>

<script>
function showTab(id,el){
    document.querySelectorAll('.tab-pane').forEach(function(p){p.classList.remove('active')});
    document.querySelectorAll('.tab-btn,.tab-link').forEach(function(b){b.classList.remove('active')});
    var p=document.getElementById(id);
    if(p)p.classList.add('active');
    if(el)el.classList.add('active');
}
function delConfirm(url,msg){
    if(confirm(msg||'Silmek istediginize emin misiniz?'))window.location=url;
}
(function(){
    var ud=document.querySelector('.tb-user');
    var dm=ud?ud.querySelector('.ud'):null;
    if(ud&&dm){
        ud.addEventListener('click',function(e){
            var open=dm.style.display==='block';
            dm.style.display=open?'none':'block';
            e.stopPropagation();
        });
        document.addEventListener('click',function(){if(dm)dm.style.display='';});
    }
    document.querySelectorAll('.main-nav .nav-item').forEach(function(item){
        var dd=item.querySelector('.nav-dd');
        if(!dd)return;
        var trigger=item.querySelector('.nl');
        if(trigger){
            trigger.addEventListener('click',function(e){
                var isOpen=dd.classList.contains('open');
                document.querySelectorAll('.nav-dd').forEach(function(d){d.classList.remove('open');});
                if(!isOpen){dd.classList.add('open');e.stopPropagation();}
            });
        }
    });
    document.addEventListener('click',function(){
        document.querySelectorAll('.nav-dd').forEach(function(d){d.classList.remove('open');});
    });
    });
    setTimeout(function(){
        document.querySelectorAll('.alert').forEach(function(a){
            a.style.transition='opacity .5s';
            a.style.opacity='0';
            setTimeout(function(){a.remove()},500);
        });
    },4500);
})();
</script>
</body>
</html>
