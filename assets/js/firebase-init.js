import { initializeApp } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging.js";

const firebaseConfig = {
  apiKey: "AIzaSyDrs1CmCwwDps3VGZPDuX-5D-XQK63AVhE",
  authDomain: "ccollection-fcc45.firebaseapp.com",
  projectId: "ccollection-fcc45",
  storageBucket: "ccollection-fcc45.appspot.com",
  messagingSenderId: "1090503349901",
  appId: "1:1090503349901:web:ee52bd956970e78a4534c4"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

Notification.requestPermission().then((permission) => {
  if (permission === 'granted') {
    console.log('🟢 الإذن بالإشعارات مقبول');

    getToken(messaging, {
      vapidKey: 'BDDaWR-ssS4jKe5r6rcLt21a_p9bJqLK5PV5i8Bv9DUWEGX9Y8OpX9tbmYMFeiieuvSAIw0ybMpplPR_fsJkm3Y'
    }).then((currentToken) => {
      if (currentToken) {
        console.log('📲 Token:', currentToken);

        fetch('/collection/index.php/NotificationController/save_token', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ token: currentToken })
        })
        .then(res => res.text())
        .then(result => {
          console.log('📩 رد السيرفر:', result);
        })
        .catch(err => {
          console.error('❌ خطأ في fetch:', err);
        });

      } else {
        console.warn('⚠️ لم يتم توليد توكن');
      }
    }).catch((err) => {
      console.error('❌ فشل في getToken:', err);
    });

  } else {
    console.warn('🚫 تم رفض الإذن بالإشعارات');
  }
});
