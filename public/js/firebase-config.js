// Firebase Configuration for Web
// Get these values from Firebase Console -> Project Settings -> General -> Your apps -> Web app

const firebaseConfig = {

  apiKey: "AIzaSyCYCmxKcnO6hH5ZQU6NghLS5R2TA_eGKSw",

  authDomain: "kandura-34abc.firebaseapp.com",

  projectId: "kandura-34abc",

  storageBucket: "kandura-34abc.firebasestorage.app",

  messagingSenderId: "928328141726",

  appId: "1:928328141726:web:b3bfb75f81a5eb6050648f",

  measurementId: "G-PME0BC223E"

};


// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = firebaseConfig;
}
