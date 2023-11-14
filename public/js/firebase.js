
  // Import the functions you need from the SDKs you need
/*   import { initializeApp } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-analytics.js"; */
    import { initializeApp } from "firebase/app";
    import { getAnalytics } from "firebase/analytics";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = { 
      apiKey: "AIzaSyBGQOy6X1kmU4bZPDI0Nmvw1xn9QB-Arvk",
      authDomain: "wasilapp-e7679.firebaseapp.com",
      projectId: "wasilapp-e7679",
      storageBucket: "wasilapp-e7679.appspot.com",
      messagingSenderId: "677303892936",
      appId: "1:677303892936:web:40840db8947775f7e01efd",
      measurementId: "G-44ZFGT8DD6"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
