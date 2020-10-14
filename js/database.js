firebase.initializeApp({
    apiKey: config["firebaseApiKey"],
    authDomain: config["firebaseAuthDomain"],
    projectId: config["firebaseProjectId"]
});

var database = firebase.firestore();