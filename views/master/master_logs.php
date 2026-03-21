<!DOCTYPE html>
<html>
<head><title>Master - Logs</title></head>
<body>
    <h1>Painel Master - Logs do Sistema</h1>
    <table border="1" width="100%">
        <tr>
            <th>Data</th>
            <th>Usuário</th>
            <th>Ação</th>
        </tr>
        <?php foreach($logs as $log): ?>
        <tr>
            <td><?= $log['data_hora'] ?></td>
            <td><?= $log['nome'] ?></td>
            <td><?= $log['acao'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="index.php?rota=home">Voltar ao PDV</a> | 
    <a href="index.php?rota=logout">Sair</a>
</body>
</html>