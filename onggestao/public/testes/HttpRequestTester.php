<!DOCTYPE html>
<html lang="ptbr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>    
    <title>Requisição HTTP com AJAX e jQuery</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        #jsonData {
            width: 100%;
            height: 100px;
        }
        #responseArea {
            white-space: pre-wrap; /* Mantém a formatação da resposta */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Requisição HTTP com AJAX e jQuery</h2>

    <label for="url">URL:</label>
    <input type="url" id="url" name="url"><br><br>

    <label for="method">Método:</label>
    <select id="method" name="method">
        <option value="GET">GET</option>
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="DELETE">DELETE</option>
    </select>
    <button id="sendRequest">Enviar Requisição</button>
    <br><br>

    <div id="tableData">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="paramName1"></td>
                    <td><input type="text" name="paramValue1"></td>
                    <td><button type="button" class="btnRemoveLine">Remove</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <button id="addRow">Adicionar Linha</button>
    <textarea id="jsonData"></textarea>
    
    <hr>
    <div id="responseArea">
        <!-- Resposta da requisição será exibida aqui -->
    </div>

<script>
    function updateJsonData() {
        var data = {};
        $('#tableData tbody tr').each(function() {
            var name = $(this).find('td input[name^="paramName"]').val();
            var value = $(this).find('td input[name^="paramValue"]').val();
            if (name) data[name] = value;
        });
        $('#jsonData').val(JSON.stringify(data, null, 2));
    }

    function updateTableFromJson() {
        var data = JSON.parse($('#jsonData').val());
        var tbody = $('#tableData tbody');
        tbody.empty();
        $.each(data, function(name, value) {
            var rowCount = tbody.children().length + 1;
            tbody.append('<tr><td><input type="text" name="paramName' + rowCount + '" value="' + name + '"></td><td><input type="text" name="paramValue' + rowCount + '" value="' + value + '"></td><td><button type="button" class="btnRemoveLine">Remove</button></td></tr>');
        });
    }

    $(document).ready(function() {
        // Atualiza o JSON quando a tabela muda
        $('#tableData').on('input', 'input', updateJsonData);
        $('#tableData').on('click', '.btnRemoveLine', function() {
            $(this).closest('tr').remove();
            updateJsonData();
        });

        // Atualiza a tabela quando o JSON muda
        $('#jsonData').on('input', updateTableFromJson);

        // Adiciona uma nova linha na tabela
        $('#addRow').click(function() {
            var rowCount = $('#tableData tbody tr').length + 1;
            $('#tableData tbody').append('<tr><td><input type="text" name="paramName' + rowCount + '"></td><td><input type="text" name="paramValue' + rowCount + '"></td><td><button type="button" class="btnRemoveLine">Remove</button></td></tr>');
            updateJsonData();
        });

        $(document).on('click', '.btnRemoveLine', function() {
            $(this).closest('tr').remove();
        });

        $('#sendRequest').click(function() {
            var url = $('#url').val();
            var method = $('#method').val();
            var data = {};

            $('#tableData tbody tr').each(function() {
                var name = $(this).find('td input[name^="paramName"]').val();
                var value = $(this).find('td input[name^="paramValue"]').val();
                if (name) data[name] = value;
            });

            $('#jsonData').val(JSON.stringify(data, null, 2));

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    $('#responseArea').text(JSON.stringify(response, null, 2));
                },
                error: function(xhr, status, error) {
                    $('#responseArea').text("Erro na requisição: " + error);
                }
            });
        });
    });
</script>
<footer style="text-align: center; padding: 20px; margin-top: 30px; border-top: 1px solid #ccc;">
    <p>ZaitTinyFrameworkPHP - HttpRequestTester</p>
    <p>Autor: Profº Msc Cleber Oliveira</p> 
    <p>Desenvolvedores: Profº Msc Cleber Oliveira, Luís Fernando S. Melo </p>
	<p><a href="https://cleberoliveira.info" target="_blank">https://cleberoliveira.info</a></p>
</footer>
</body>
</html>
