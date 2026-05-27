<style>
  /* Popup container */
  .cookie-consent-popup {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #333333b8;
    color: #fff;
    padding: 15px;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    text-align: center;
  }

  /* Popup content */
  .cookie-consent-content {
    max-width: 800px;
    margin: 0 auto;
  }

  /* Buttons */
  .cookie-consent-popup button {
    background-color: #d81418;
    border: none;
    color: white;
    padding: 10px 20px;
    margin: 5px;
    cursor: pointer;
  }

  .cookie-consent-popup button:hover {
    background-color: #910b0e;
  }

  .cookie-consent-popup a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
  }

  .cookie-consent-popup a:hover {
    text-decoration: underline;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const cookieConsentPopup = document.getElementById('cookieConsentPopup');
    const acceptCookiesButton = document.getElementById('acceptCookies');
    const declineCookiesButton = document.getElementById('declineCookies');

    // Function to get a cookie by name
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Check the user's cookie consent status
    function checkCookieConsent() {
      const consent = getCookie('cookieConsent');
      console.log('Current cookie consent status:', consent);
      return consent;
    }

    // Show the popup if no decision has been made yet
    function showPopup() {
      const consent = checkCookieConsent();
      if (!consent) {
        console.log('Showing cookie consent popup.');
        cookieConsentPopup.style.display = 'block';
      }
    }

    function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      const expires = "expires=" + d.toUTCString();
      document.cookie = name + "=" + (value || "") + ";" + expires + ";path=/";
      console.log(`Cookie set: ${name}=${value}, expires in ${days} days.`);
    }

    acceptCookiesButton.addEventListener('click', function() {
      console.log('User accepted cookies.');
      setCookie('cookieConsent', 'accepted', 365);
      cookieConsentPopup.style.display = 'none';
    });

    declineCookiesButton.addEventListener('click', function() {
      setCookie('cookieConsent', 'declined', 1); // Cookie expires in 1 day
      cookieConsentPopup.style.display = 'none';
    });

    showPopup();
  });
</script>
<div id="cookieConsentPopup" class="cookie-consent-popup">
  <div class="cookie-consent-content">
    <p>We use cookies to improve your experience on our site. By using our site, you consent to our use of cookies. <a
        href="privacy-policy.php">Read Our Privacy Policy</a></p>
    <button id="acceptCookies">Accept</button>
    <button id="declineCookies">Decline</button>
  </div>
</div>