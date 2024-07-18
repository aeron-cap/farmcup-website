function updateClock() {
    const now = new Date();
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    let amPm = '';

    // AM/PM
    if (hours >= 12) {
        amPm = 'PM';
        hours -= 12;
    } else {
        amPm = 'AM';
    }

    if (hours === 0) {
        hours = 12;
    }

    //Format ng Time
    const timeString = `${hours}:${minutes}:${seconds} ${amPm}`;
    document.querySelector('.live-clock').textContent = timeString;
}

updateClock();
setInterval(updateClock, 1000);



function updateDate() {
    const now = new Date();
    const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const monthsOfYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const dayOfWeek = daysOfWeek[now.getDay()];
    const monthName = monthsOfYear[now.getMonth()];
    const day = String(now.getDate()).padStart(2, '0');
    const year = now.getFullYear();

    const dateString = `${dayOfWeek}, ${monthName} ${day}, ${year}`;
    document.querySelector('.live-date').textContent = dateString;
}

updateDate();
setInterval(updateDate, 1000);