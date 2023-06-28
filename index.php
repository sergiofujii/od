<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Alunos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Consulta de Alunos</h2>

        <form method="POST">
            <div class="form-row">
                <div class="col">
                    <label for="filtroAlunoId">Aluno ID:</label>
                    <input type="text" class="form-control filtro" id="filtroAlunoId" name="filtroAlunoId">
                </div>
                <div class="col">
                    <label for="filtroMatricula">Matrícula:</label>
                    <input type="text" class="form-control filtro" id="filtroMatricula" name="filtroMatricula">
                </div>
                <div class="col">
                    <label for="filtroNome">Nome:</label>
                    <input type="text" class="form-control filtro" id="filtroNome" name="filtroNome">
                </div>
                <div class="col">
                    <label for="filtroDiario">Diário:</label>
                    <input type="text" class="form-control filtro" id="filtroDiario" name="filtroDiario">
                </div>
                <div class="col">
                    <label for="filtroComponente">Componente:</label>
                    <input type="text" class="form-control filtro" id="filtroComponente" name="filtroComponente">
                </div>
                <div class="col">
                    <label for="filtroSituacao">Situação:</label>
                    <input type="text" class="form-control filtro" id="filtroSituacao" name="filtroSituacao">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
            <button type="reset" class="btn btn-secondary mt-3 ml-2">Limpar</button>
            <button type="button" class="btn btn-secondary" onclick="limparFiltros()">Limpar Filtros</button>

        </form>

        <hr>

        <form method="POST">
            <div class="form-group">
                <label for="consultaSql">Consulta SQL:</label>
                <textarea class="form-control" id="consultaSql" name="consultaSql" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Executar Consulta</button>
        </form>

        <hr>

        <?php
        // Configuração do banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "od";

        // Conexão com o banco de dados
        $conn = new mysqli($servername, $username, $password, $dbname);


        
        function limparEntrada($conn, $dados) {
            // Verifica se a diretiva magic_quotes_gpc está ativada
            if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
                // Remove as barras de escape adicionadas automaticamente
                $dados = stripslashes($dados);
            }
        
            // Evita caracteres especiais que possam ser usados em ataques
            $dados = mysqli_real_escape_string($conn, $dados);
        
            return $dados;
        }
        
        // Limpa os campos de filtro
        $filtroAlunoId = isset($_GET['filtroAlunoId']) ? limparEntrada($conn, $_GET['filtroAlunoId']) : '';
        $filtroMatricula = isset($_GET['filtroMatricula']) ? limparEntrada($conn, $_GET['filtroMatricula']) : '';
        $filtroNome = isset($_GET['filtroNome']) ? limparEntrada($conn, $_GET['filtroNome']) : '';
        $filtroDiario = isset($_GET['filtroDiario']) ? limparEntrada($conn, $_GET['filtroDiario']) : '';
        $filtroComponente = isset($_GET['filtroComponente']) ? limparEntrada($conn, $_GET['filtroComponente']) : '';
        $filtroSituacao = isset($_GET['filtroSituacao']) ? limparEntrada($conn, $_GET['filtroSituacao']) : '';
        
        // Consulta SQL
        $consultaSql = isset($_POST['consultaSql']) ? $_POST['consultaSql'] : '';

        // Executa a consulta SQL se o campo não estiver vazio
        if (!empty($consultaSql)) {
            try {
                $result = $conn->query($consultaSql);
                echo "<a id='inicio-tabela'></a>";

                if ($result === false) {
                    throw new Exception("Erro na consulta SQL");
                }

                echo "<h3>Resultado da Consulta SQL</h3>";
                echo "<p>Total de linhas retornadas: " . $result->num_rows . "</p>";

                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Aluno ID</th>
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Diário</th>
                                    <th>Componente</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>";

                            // Tirando N/A
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . (isset($row['aluno_id']) ? $row['aluno_id'] : '-') . "</td>
                                    <td>" . (isset($row['matricula']) ? $row['matricula'] : '-') . "</td>
                                    <td>" . (isset($row['nome']) ? $row['nome'] : '-') . "</td>
                                    <td>" . (isset($row['diario']) ? $row['diario'] : '-') . "</td>
                                    <td>" . (isset($row['componente']) ? $row['componente'] : '-') . "</td>
                                    <td>" . (isset($row['situacao']) ? $row['situacao'] : '-') . "</td>
                                </tr>";
                            }

                    echo "</tbody>
                        </table>";
                } else {
                    echo "<p>Nenhum resultado encontrado.</p>";
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger' role='alert'>
                        Erro na consulta SQL: " . $e->getMessage() . "
                    </div>";
            }
        }


        // Constrói a consulta SQL com base nos filtros
        $sql = "SELECT * FROM alunos WHERE aluno_id LIKE '%$filtroAlunoId%' AND matricula LIKE '%$filtroMatricula%' AND nome LIKE '%$filtroNome%' AND diario LIKE '%$filtroDiario%' AND componente LIKE '%$filtroComponente%' AND situacao LIKE '%$filtroSituacao%'";

        // Executa a consulta SQL
        $result = $conn->query($sql);

        if ($result === false) {
            echo "Erro na consulta: " . $conn->error;
        } else {
            echo "<h3>Resultado da Consulta</h3>";
            echo "<p>Total de linhas retornadas: <span id='totalLinhas'>" . $result->num_rows . "</span></p>";

            echo "<table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Aluno ID</th>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>Diário</th>
                            <th>Componente</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['aluno_id'] . "</td>
                        <td>" . $row['matricula'] . "</td>
                        <td>" . $row['nome'] . "</td>
                        <td>" . $row['diario'] . "</td>
                        <td>" . $row['componente'] . "</td>
                        <td>" . $row['situacao'] . "</td>
                    </tr>";
            }

            echo "</tbody>
                </table>";
        }

        // Fecha a conexão com o banco de dados
        $conn->close();
        ?>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script>
            $(document).ready(function() {
                // Atualizar total de linhas
                function atualizarTotalLinhas() {
                    var totalLinhas = $("table tbody tr:visible").length;
                    $("#totalLinhas").text(totalLinhas);
                }

                // Filtro dinâmico
                $(".filtro").keyup(function() {
                    var filtroAlunoId = $("#filtroAlunoId").val().toLowerCase();
                    var filtroMatricula = $("#filtroMatricula").val().toLowerCase();
                    var filtroNome = $("#filtroNome").val().toLowerCase();
                    var filtroDiario = $("#filtroDiario").val().toLowerCase();
                    var filtroComponente = $("#filtroComponente").val().toLowerCase();
                    var filtroSituacao = $("#filtroSituacao").val().toLowerCase();

                    $("table tbody tr").each(function() {
                        var alunoId = $(this).find("td:nth-child(1)").text().toLowerCase();
                        var matricula = $(this).find("td:nth-child(2)").text().toLowerCase();
                        var nome = $(this).find("td:nth-child(3)").text().toLowerCase();
                        var diario = $(this).find("td:nth-child(4)").text().toLowerCase();
                        var componente = $(this).find("td:nth-child(5)").text().toLowerCase();
                        var situacao = $(this).find("td:nth-child(6)").text().toLowerCase();

                        $(this).toggle(
                            alunoId.indexOf(filtroAlunoId) > -1 &&
                            matricula.indexOf(filtroMatricula) > -1 &&
                            nome.indexOf(filtroNome) > -1 &&
                            diario.indexOf(filtroDiario) > -1 &&
                            componente.indexOf(filtroComponente) > -1 &&
                            situacao.indexOf(filtroSituacao) > -1
                        );
                    });

                    atualizarTotalLinhas();
                });

                // Limpar campos de filtro
                $("button[type='reset']").click(function() {
                    $(".filtro").val("");
                    $("table tbody tr").show();
                    atualizarTotalLinhas();
                });
            });
        </script>

        <script>
            function limparFiltros() {
                // Limpar campos de filtro
                $("#filtroAlunoId").val("");
                $("#filtroMatricula").val("");
                $("#filtroNome").val("");
                $("#filtroDiario").val("");
                $("#filtroComponente").val("");
                $("#filtroSituacao").val("");

                // Redirecionar para o início da tabela
                window.location.href = "#inicio-tabela";
            }
        </script>



    </div>
</body>

</html>
