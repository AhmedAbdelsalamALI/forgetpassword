
    const userId = localStorage.getItem("user_id");
    const userName = localStorage.getItem("user_name");

    if (!userId || !userName) {
      window.location.replace("../index.html")
    }
