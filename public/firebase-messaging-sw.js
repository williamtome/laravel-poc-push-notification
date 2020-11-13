importScripts('https://www.gstatic.com/firebasejs/7.14.4/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.14.4/firebase-messaging.js');

var firebaseConfig = {
    apiKey: "AIzaSyAAa4h_9YYKNwXxrutTSvmV2bHlR4slPUA",
    authDomain: "poc-web-push-d0ce6.firebaseapp.com",
    databaseURL: "https://poc-web-push-d0ce6.firebaseio.com",
    projectId: "poc-web-push-d0ce6",
    storageBucket: "poc-web-push-d0ce6.appspot.com",
    messagingSenderId: "296335342701",
    appId: "1:296335342701:web:137a4083cd725c02799364"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Recebida mensagem em background ', payload);
  // Customize notification here
  const notificationTitle = payload.notification.title;
  const notificationMessage = payload.notification.body;

  return self.registration.showNotification(notificationTitle,
    notificationMessage);
});
