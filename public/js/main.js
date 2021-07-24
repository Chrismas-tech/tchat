function getCurrentTime() {
    return moment().format('LT');
}

function getCurrent_Date_and_Time() {
    $format = moment().format();
    return $date = $format.substring(10, -1) + ' ' + moment().format('LT');
}