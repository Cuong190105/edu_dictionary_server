document.addEventListener("DOMContentLoaded", function () {
    const field = document.getElementById("searchfield");
    const searchbar = document.getElementById("searchbar");
    const clearBtn = document.getElementById("clear-btn");
    searchbar.onkeydown = (event) => {
        if (event.key === "Enter" && field.value.length == 0) {
            event.preventDefault();
        }
    }
    field.addEventListener("input", updateClearButtonVisibility);

    clearBtn.addEventListener("click", function () {
        clearBtn.style.visibility = "hidden";
    });

    function updateClearButtonVisibility() {
        if (field.value.length > 0) {
            clearBtn.style.visibility = "visible";
        } else {
            clearBtn.style.visibility = "hidden";
        }
    }
});