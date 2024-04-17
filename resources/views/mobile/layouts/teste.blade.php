<!DOCTYPE html>
<html lang="en">
<meta charset="utf8">
<title>Slip.js — sortable and swipeable views</title>
<meta name="viewport" content="width=device-width, user-scalable=no, maximum-scale=1.0">
<style>
    .slippylist li {
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        cursor: default;
    }

    .slippylist li {
        display: block;
        position: relative;
        border: 1px solid black;
    }

</style>
<body>
<ul id="slippyList" class="slippylist">
    <li class="demo-no-swipe">hold &amp; reorder <span class="instant">or instantly</span></li>
    <li>iOS Safari</li>
    <li>Mobile Chrome</li>
    <li>No dependencies</li>
</ul>

<script src="{{ asset('assets/mobile/js/slip.js') }}"></script>
<script>
    function setupSlip(list) {
        list.addEventListener('slip:beforereorder', function(e){
            if (e.target.classList.contains('demo-no-reorder')) {
                e.preventDefault();
            }
        }, false);
        list.addEventListener('slip:beforeswipe', function(e){
            if (e.target.nodeName == 'INPUT' || e.target.classList.contains('demo-no-swipe')) {
                e.preventDefault();
            }
        }, false);
        list.addEventListener('slip:beforewait', function(e){
            if (e.target.classList.contains('instant')) e.preventDefault();
        }, false);
        list.addEventListener('slip:afterswipe', function(e){
            e.target.parentNode.appendChild(e.target);
        }, false);
        list.addEventListener('slip:reorder', function(e){
            e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
            return false;
        }, false);
        return new Slip(list);
    }
    setupSlip(document.getElementById('slippyList'));
</script>