(function () {
  const consentKey = 'desnky_cookie_consent_v1';
  const banner = document.querySelector('[data-cookie-consent]');
  const acceptButton = document.querySelector('[data-cookie-accept]');
  const declineButton = document.querySelector('[data-cookie-decline]');
  let adsLoaded = false;

  function readConsent() {
    try {
      return JSON.parse(localStorage.getItem(consentKey) || 'null');
    } catch (error) {
      return null;
    }
  }

  function writeConsent(adsAllowed) {
    try {
      localStorage.setItem(consentKey, JSON.stringify({
        ads: Boolean(adsAllowed),
        updatedAt: new Date().toISOString()
      }));
    } catch (error) {
      // Storage may be unavailable in private browsing; keep the page usable.
    }
  }

  function showBannerIfNeeded() {
    if (!banner) {
      return;
    }

    const consent = readConsent();
    if (!consent) {
      banner.classList.remove('hidden');
    }
  }

  function hideBanner() {
    if (banner) {
      banner.classList.add('hidden');
    }
  }

  function loadAdsIfAllowed() {
    const consent = readConsent();
    if (!consent || consent.ads !== true || adsLoaded) {
      return;
    }

    const units = Array.from(document.querySelectorAll('.adsbygoogle'));
    if (units.length === 0) {
      return;
    }

    const client = units.map((unit) => unit.dataset.adClient).find(Boolean);
    if (!client) {
      return;
    }

    adsLoaded = true;
    const script = document.createElement('script');
    script.async = true;
    script.crossOrigin = 'anonymous';
    script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' + encodeURIComponent(client);
    script.onload = function () {
      units.forEach(function () {
        try {
          window.adsbygoogle = window.adsbygoogle || [];
          window.adsbygoogle.push({});
        } catch (error) {
          // Ad blockers or network errors should not affect editorial content.
        }
      });
    };
    document.head.appendChild(script);
  }

  if (acceptButton) {
    acceptButton.addEventListener('click', function () {
      writeConsent(true);
      hideBanner();
      loadAdsIfAllowed();
    });
  }

  if (declineButton) {
    declineButton.addEventListener('click', function () {
      writeConsent(false);
      hideBanner();
    });
  }

  showBannerIfNeeded();
  loadAdsIfAllowed();
})();
