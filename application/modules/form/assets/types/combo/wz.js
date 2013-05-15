$("#idop").change(function(){
    url='../types/combo/options.wz.php?idop='+$(this).val()+'&selected=<?=$f['default'];?>';
    $("#default").load(url);
});
$("#idop").trigger('change');