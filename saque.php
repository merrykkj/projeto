<?php
session_start();
 
$saque = 0; // Definindo uma variável inicial para o saque
$mostrarFormulario = true; // Controle para mostrar o formulário de saque
 
 
 
?>
 
<!DOCTYPE html>
<html lang="pt-br">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saque - BancoFlacko</title>
    <script src="funcoes.js"></script>
    <link rel="stylesheet" href="sacar.css">
    <style>
        #inputSaque,
        #teclado {
            display: block;
        }
    </style>
</head>
 
<body>
 <main>

 <div class="saldo-container"> 

    <!-- if para a condição do botão sim ou não do saque de não múltiplo -->
    <?php if ($mostrarFormulario): ?>
        <?php
    if (!isset($_SESSION['limite'])) {
    $_SESSION['limite'] = 0; // limite inicial de saque
}
 
echo "<h2 class='saldo'>Notas disponíveis: R$10,00, R$20,00, R$50,00, R$100. <br></h2>";
echo "<h2 class='saldo'><br> Limite Diário: R$2000,00. <br></h2>";
 
if (isset($_SESSION['saldo'])) {
    echo "<h2 class='saldo'><br>Saldo da conta: R$" . $_SESSION['saldo'] . ",00  <br><br>";
} else {
    echo "<h2 class='saldo'>Saldo da conta: R$0,00 <br><br></h2>";
}
 ?>
 </div>
     <section class="deposito-container">
     <h1 class="titulo">SAQUE</h1>
     <form action="saque.php" method="post" id="formSaque">
        <div class="teclado">
        <input type="text" id="saque" name="enviar" value=""  id="inputSaque"> <br> <br>
            <button class="botoes" type="button" onclick="adicionarNumero('1')">1</button>
            <button class="botoes" type="button" onclick="adicionarNumero('2')">2</button>
            <button class="botoes" type="button" onclick="adicionarNumero('3')">3</button><br>
            <button class="botoes" type="button" onclick="adicionarNumero('4')">4</button>
            <button class="botoes" type="button" onclick="adicionarNumero('5')">5</button>
            <button class="botoes" type="button" onclick="adicionarNumero('6')">6</button><br>
            <button class="botoes" type="button" onclick="adicionarNumero('7')">7</button>
            <button class="botoes" type="button" onclick="adicionarNumero('8')">8</button>
            <button class="botoes" type="button" onclick="adicionarNumero('9')">9</button><br>
            <button class="botoes" type="submit">Enviar</button>
            <button class="botoes" type="button" onclick="adicionarNumero('0')">0</button>
            <button class="botoes" type="button" onclick="limparCampo()">Limpar</button><br>

        </div>
    </section>
 

    </form>

    <?php
 
// Verificando se a requisição foi POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe o valor digitado no campo de entrada
 
    if (!isset($_SESSION["saldo"])) {
        echo "<p style='color: red;'>Saldo Insuficiente.</p>";
    }else{
 
   
 
 
   
    if (isset($_POST['enviar'])) {
        $saque = filter_input(INPUT_POST, 'enviar', FILTER_VALIDATE_INT);
        if ($saque === false || $saque < 10) {
            echo "<p style='color: red;'>Valor de saque inválido. O valor mínimo de saque é R$10,00.</p>";
        } elseif ($saque % 10 != 0) {
            // Caso o valor não seja múltiplo de 10, pede para arredondar
            $saqueArredondado = floor($saque / 10) * 10;
            echo "<h2 class='nota' Infelizmente não temos notas para imprimir esse valor, deseja arredondar para um valor mais baixo? O valor arredondado seria: R$$saqueArredondado,00";
            echo '<form action="saque.php" method="post">';
            echo "<button type='submit' name='sim'>Sim</button>";
            echo "<button type='submit' name='nao'>Não</button>";
            echo "<input type='hidden' name='saque' value='$saque'/>"; // Passando o valor do saque
            echo '</form>';
 
            // Alterando o controle para não mostrar o formulário de saque
            $mostrarFormulario = false;
        } else {
            // Verificando se o valor é válido para saque
            if ($saque <= $_SESSION['saldo'] && $_SESSION['limite'] + $saque <= 2000) {
                $_SESSION['saldo'] -= $saque;
                $_SESSION['limite'] += $saque;
                echo "<p class='sucesso' style='color: orange;'>Limite total após saque: R$" . $_SESSION['limite'] . ",00</p>";
                echo "<p class='sucesso' style='color: lime;'>Saque de R$$saque efetuado com sucesso!</p>";
            } else {
                echo "<p class='invalido' style='color: red;'>Valor superior ao saldo disponível ou limite diário atingido.</p>";
            }
        }
    }
   
 
    // se o usuário selecionar o botão sim
    if (isset($_POST['sim'])) {
        // Captura o valor do saque que foi passado através de um input hidden
        $saque = $_POST['saque'];
        $saqueArredondado = floor($saque / 10) * 10; // Arredonda para o múltiplo de 10 mais próximo
 
        if ($saqueArredondado <= $_SESSION['saldo'] && $_SESSION['limite'] + $saqueArredondado <= 2000) {
            $_SESSION['saldo'] -= $saqueArredondado; // Subtrai o valor arredondado do saldo
            $_SESSION['limite'] += $saqueArredondado; // Atualiza o limite
            echo "<p class='sucesso' style='color: lime;'>Limite total após saque: R$" . $_SESSION['limite'] . ",00</p>";
            echo "<p class='sucesso' style='color: lime;'>Saque de R$$saqueArredondado efetuado com sucesso!</p>";
        } else {
            echo "<p class='erro' style='color: red;'>Não foi possível realizar o saque, saldo ou limite insuficientes.</p>";
        }
    }
 
    // se o usuário selecionar botão não
    if (isset($_POST['nao'])) {
        echo "Operação cancelada. Nenhum valor foi arredondado.";
    }
    $_SESSION['saque'] = $saque;
 
}
}
?>

    <?php endif; ?>
</main>
    <footer>
        <button class="botao-footer"><a href="./index.php">Inicio</a></button>
        <div class="horario">
        <?php
        // data atual
        $dataAtual = new DateTime();
        $timezone = new DateTimeZone('America/Sao_Paulo');
 
        $dataAtual->setTimezone($timezone);
        echo $dataAtual->format('d/F /Y à\s h:i');
 
 
        ?>
        </div>
    </footer>
 
 
</body>
</html>
