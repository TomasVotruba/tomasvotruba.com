// inspired by http://phppackagechecklist.com/js/scripts.js

$(function() {
    // Set default value
    if (window.localStorage.getItem('clusters') == null) {
        saveClusters([]);
    }

    var $clusters = getClusters();

    $clusters.forEach(function (element) {
        $('#' + element).addClass('active-cluster');
    });

    // activate!
    $('.make_active').click(function() {
        var $clusters = getClusters();

        var $id = $(this).data('id');
        $clusters.push($id);

        $('#' + $id).addClass('active-cluster');

        saveClusters($clusters);
    });

    // passivate!
    $('.make_passive').click(function() {
        var $clusters = getClusters();

        var $id = $(this).data('id');
        $clusters.pop($id);

        $('#' + $id).removeClass('active-cluster');

        saveClusters($clusters);
    });


    function getClusters(data) {
        data = window.localStorage.getItem('clusters');
        return JSON.parse(data);
    }

    function saveClusters(data) {
        data = arrayUnique(data);
        window.localStorage.setItem('clusters', JSON.stringify(data));
    }

    // @see https://stackoverflow.com/a/17936490/1348344
    function sleepFor(sleepDuration) {
        var now = new Date().getTime();
        while(new Date().getTime() < now + sleepDuration){ /* do nothing */ }
    }

    function arrayUnique(values) {
        return values.filter(onlyUnique);
    }

    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }
});
