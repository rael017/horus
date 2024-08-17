document.addEventListener('DOMContentLoaded', function() {
    var selectElement = document.getElementById('pagina');

    selectElement.addEventListener('change', function() {
        var selectedOption = selectElement.value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'App/Controllers/Admin/Site.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                // Faça o que precisa ser feito com a resposta do servidor
                console.log(response.selectedOption);
            }
        };

        xhr.send('pagina=' + selectedOption);
    });
});
