document.addEventListener("DOMContentLoaded", function () {
  const editModal = document.getElementById("editAnime");

  if (editModal) {
    editModal.addEventListener("show.bs.modal", function (event) {
      // Button that triggered the modal
      const button = event.relatedTarget;

      // Extract info from data-* attributes
      const id = button.getAttribute("data-id");
      const title = button.getAttribute("data-title");
      const studio = button.getAttribute("data-studio");
      const type = button.getAttribute("data-type");
      const current = button.getAttribute("data-current");
      const total = button.getAttribute("data-total");
      const release = button.getAttribute("data-release");
      const rating = button.getAttribute("data-rating");
      const status = button.getAttribute("data-status");
      const cover = button.getAttribute("data-cover");

      // Update the modal's content values
      document.getElementById("editId").value = id;
      document.getElementById("editAnimeTitle").value = title;
      document.getElementById("editAnimeStudio").value = studio;
      document.getElementById("editAnimeType").value = type;
      document.getElementById("editCurrentEpisode").value = current;
      document.getElementById("editTotalEpisodes").value = total;
      document.getElementById("editReleaseYear").value = release;
      document.getElementById("editAnimeScore").value = rating;
      document.getElementById("editWatchStatus").value = status;
      document.getElementById("editAnimePosterUrl").value = cover;
    });
  }
});
