<div class="social-share">
    <ul>
        <ul class="share-widget">
            <li><a class="link facebook" href="" target="_blank" onclick="return fbs_click()"><i class="facebook fa-brands fa-facebook-f"></i></a></li>
            <li><a class="link twitter" href="" target="_blank" onclick="return tbs_click()"><i class="twitter fa-brands fa-twitter"></i></a></li>
            <li><a class="link wp" href="" target="_blank" onclick="return wbs_click()"><i class="whatsapp1 fa-brands fa-whatsapp"></i></a></li>
            <li><a class="link google" href="" target="_blank" onclick="return lbs_click()"><i class="linkedin fa-brands fa-linkedin-in"></i></a></li>
        </ul>
    </ul>
</div>

<script>
    var pageLink = window.location.href;
    var pageTitle = String(document.title).replace(/\&/g, '%26');

    function fbs_click() {
        window.open(`http://www.facebook.com/sharer.php?u=${pageLink}&quote=${pageTitle}`, 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    }

    function tbs_click() {
        window.open(`https://twitter.com/intent/tweet?text=${pageTitle}&url=${pageLink}`, 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    }

    function lbs_click() {
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${pageLink}`, 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    }

    function wbs_click() {
        window.open(`whatsapp://send?text=${pageLink}`, 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return false;
    }
</script>