
$(document).ready(function () {

  
    $('#contatoForm').submit(function (event) {
        event.preventDefault(); // Evita o comportamento padrão do formulário

        var msg = $('#msg');
        msg.hide();
        clear();

        // Realiza a validação do lado do cliente
        // Obtém os dados do formulário
        var formData = $(this).serialize();

        // Adiciona uma flag para identificar a requisição Ajax
        formData += '&acao=1';  // Certifique-se de ajustar isso conforme necessário

        // Envia a requisição Ajax
        $.ajax({
            type: 'POST',
            url: 'http://localhost/codeMind/',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status == 'success') {
                    msg.text(response.message).show();
                    msg.addClass('success-message');
                    
                    // Exibe a mensagem de sucesso no console para depuraçã
        
                    // Oculta a mensagem de sucesso após 3 segundos
                    setTimeout(function () {
                        msg.hide();
                        $('html, body').animate({ scrollTop: 0 }, 500);
                    }, 3000);
        
                    // Limpa o formulário após 3 segundos (se desejar)
                    setTimeout(function () {
                        $('#contatoForm')[0].reset();
                    }, 3000);
        
                } else {
                     msg.text(response.message).show();
                     msg.addClass('error-message');
                     
                }
            },
            error: function () {
                // Mensagem de erro genérica
                alert('Erro ao enviar mensagem.');
            }
        });
    });

    // Função para limpar mensagens de erro
    function clear() {
        $('#msg').text('').hide();
    }
});