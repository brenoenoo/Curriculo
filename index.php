<?php
require_once 'crud.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'edit_perfil') {
        
        $foto_url = $_POST['foto_url_atual'] ?? '';
        $banner_url = $_POST['banner_url_atual'] ?? '';
        
        $diretorio_destino = 'uploads/';

        if (!is_dir($diretorio_destino)) {
            mkdir($diretorio_destino, 0777, true);
        }

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao_foto = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $novo_nome_foto = uniqid('foto_') . '.' . $extensao_foto;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio_destino . $novo_nome_foto)) {
                $foto_url = $diretorio_destino . $novo_nome_foto;
            }
        }

        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $extensao_banner = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
            $novo_nome_banner = uniqid('banner_') . '.' . $extensao_banner;
            if (move_uploaded_file($_FILES['banner']['tmp_name'], $diretorio_destino . $novo_nome_banner)) {
                $banner_url = $diretorio_destino . $novo_nome_banner;
            }
        }

        update($pdo, 'perfil', [
            'nome' => $_POST['nome'],
            'bio' => $_POST['bio'],
            'dados_pessoais' => $_POST['dados_pessoais'],
            'foto_url' => $foto_url,
            'banner_url' => $banner_url
        ], "id = 1"); 
    }

    elseif ($acao === 'add_experiencia') {
        create($pdo, 'experiencias', [
            'cargo' => $_POST['cargo'],
            'empresa' => $_POST['empresa'],
            'periodo' => $_POST['periodo'],
            'descricao' => $_POST['descricao']
        ]); 
    } 
    elseif ($acao === 'edit_experiencia') {
        update($pdo, 'experiencias', [
            'cargo' => $_POST['cargo'],
            'empresa' => $_POST['empresa'],
            'periodo' => $_POST['periodo'],
            'descricao' => $_POST['descricao']
        ], "id = " . (int)$_POST['id']); 
    }

    elseif ($acao === 'add_formacao') {
        create($pdo, 'formacoes', [
            'curso' => $_POST['curso'],
            'instituicao' => $_POST['instituicao'],
            'periodo' => $_POST['periodo']
        ]); 
    } 

    elseif ($acao === 'edit_formacao') {
        update($pdo, 'formacoes', [
            'curso' => $_POST['curso'],
            'instituicao' => $_POST['instituicao'],
            'periodo' => $_POST['periodo']
        ], "id = " . (int)$_POST['id']); 
    }

    elseif ($acao === 'add_competencia') {
        create($pdo, 'competencias', [
            'nome' => $_POST['nome']
        ]); 
    }

    header("Location: index.php");
    exit;
}

$perfil = read($pdo, 'perfil', 'id = 1'); 
$experiencias = readAll($pdo, 'experiencias'); 
$formacoes = readAll($pdo, 'formacoes'); 
$competencias = readAll($pdo, 'competencias'); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currículo</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <div class="banner" style="background-image: url('<?= htmlspecialchars($perfil['banner_url'] ?? '') ?>');"></div>
        <div class="profile-section">
            <div class="profile-photo" style="background-image: url('<?= htmlspecialchars($perfil['foto_url'] ?? '') ?>');"></div>
            
            <button class="btn btn-outline btn-edit" 
                    data-nome="<?= htmlspecialchars($perfil['nome'] ?? '') ?>"
                    data-bio="<?= htmlspecialchars($perfil['bio'] ?? '') ?>"
                    data-dados="<?= htmlspecialchars($perfil['dados_pessoais'] ?? '') ?>"
                    onclick="abrirModalEditPerfil(this)">Editar Perfil</button>
            
            <div class="profile-info">
                <h1><?= htmlspecialchars($perfil['nome'] ?? 'Seu Nome') ?></h1>
                <p class="text-muted"><?= htmlspecialchars($perfil['bio'] ?? '') ?></p>
                <p><small><?= htmlspecialchars($perfil['dados_pessoais'] ?? '') ?></small></p>
            </div>
        </div>
    </div>

    <!-- Experiencias -->
    <div class="card section-content">
        <button class="btn btn-outline btn-add" onclick="abrirModal('modalAddExperiencia')">+ Adicionar</button>
        <h2>Experiência</h2>
        <?php foreach ($experiencias as $exp): ?>
            <div class="item">
                <h3><?= htmlspecialchars($exp['cargo']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($exp['empresa']) ?> • <?= htmlspecialchars($exp['periodo']) ?></p>
                <p><?= nl2br(htmlspecialchars($exp['descricao'])) ?></p>
                
                <div class="actions-group">
                    <button class="btn btn-outline"
                            data-id="<?= $exp['id'] ?>"
                            data-cargo="<?= htmlspecialchars($exp['cargo']) ?>"
                            data-empresa="<?= htmlspecialchars($exp['empresa']) ?>"
                            data-periodo="<?= htmlspecialchars($exp['periodo']) ?>"
                            data-descricao="<?= htmlspecialchars($exp['descricao']) ?>"
                            onclick="abrirModalEditExperiencia(this)">Editar</button>
                            
                    <a href="processa_exclusao.php?tabela=experiencias&id=<?= $exp['id'] ?>" class="btn btn-outline btn-danger" onclick="return confirm('Excluir experiência?');">Excluir</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formações -->
    <div class="card section-content">
        <button class="btn btn-outline btn-add" onclick="abrirModal('modalAddFormacao')">+ Adicionar</button>
        <h2>Formação Acadêmica</h2>
        <?php foreach ($formacoes as $formacao): ?>
            <div class="item">
                <h3><?= htmlspecialchars($formacao['instituicao']) ?></h3>
                <p><?= htmlspecialchars($formacao['curso']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($formacao['periodo']) ?></p>
                
                <div class="actions-group">
                    <button class="btn btn-outline"
                            data-id="<?= $formacao['id'] ?>"
                            data-curso="<?= htmlspecialchars($formacao['curso']) ?>"
                            data-instituicao="<?= htmlspecialchars($formacao['instituicao']) ?>"
                            data-periodo="<?= htmlspecialchars($formacao['periodo']) ?>"
                            onclick="abrirModalEditFormacao(this)">Editar</button>
                            
                    <a href="processa_exclusao.php?tabela=formacoes&id=<?= $formacao['id'] ?>" class="btn btn-outline btn-danger" onclick="return confirm('Excluir formação?');">Excluir</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Competências -->
    <div class="card section-content">
        <button class="btn btn-outline btn-add" onclick="abrirModal('modalAddCompetencia')">+ Adicionar</button>
        <h2>Competências</h2>
        <div>
            <?php foreach ($competencias as $comp): ?>
                <span class="badge">
                    <?= htmlspecialchars($comp['nome']) ?>
                    <a href="processa_exclusao.php?tabela=competencias&id=<?= $comp['id'] ?>" class="badge-delete" onclick="return confirm('Excluir competência?');">&times;</a>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Editar Perfil -->
<div id="modalEditPerfil" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEditPerfil')">&times;</span>
        <h2>Editar Perfil</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="edit_perfil">
            <input type="hidden" name="foto_url_atual" value="<?= htmlspecialchars($perfil['foto_url'] ?? '') ?>">
            <input type="hidden" name="banner_url_atual" value="<?= htmlspecialchars($perfil['banner_url'] ?? '') ?>">

            <div class="form-group">
                <label>Foto de Perfil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label>Imagem do Banner</label>
                <input type="file" name="banner" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" id="edit_perfil_nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Bio Profissional</label>
                <textarea name="bio" id="edit_perfil_bio" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Dados Pessoais (Contato)</label>
                <input type="text" name="dados_pessoais" id="edit_perfil_dados" class="form-control">
            </div>
            <button type="submit" class="btn btn-submit">Salvar Perfil</button>
        </form>
    </div>
</div>

<!-- Modal Adicionar Experiencia -->
<div id="modalAddExperiencia" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalAddExperiencia')">&times;</span>
        <h2>Adicionar Experiência</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="add_experiencia">
            <div class="form-group"><label>Cargo</label><input type="text" name="cargo" class="form-control" required></div>
            <div class="form-group"><label>Empresa</label><input type="text" name="empresa" class="form-control" required></div>
            <div class="form-group"><label>Período (Ex: Jan 2020 - Atual)</label><input type="text" name="periodo" class="form-control" required></div>
            <div class="form-group"><label>Descrição das atividades</label><textarea name="descricao" class="form-control"></textarea></div>
            <button type="submit" class="btn btn-submit">Salvar Experiência</button>
        </form>
    </div>
</div>

<!-- Modal Editar Experiencia -->
<div id="modalEditExperiencia" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEditExperiencia')">&times;</span>
        <h2>Editar Experiência</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="edit_experiencia">
            <input type="hidden" name="id" id="edit_exp_id">
            <div class="form-group"><label>Cargo</label><input type="text" name="cargo" id="edit_exp_cargo" class="form-control" required></div>
            <div class="form-group"><label>Empresa</label><input type="text" name="empresa" id="edit_exp_empresa" class="form-control" required></div>
            <div class="form-group"><label>Período</label><input type="text" name="periodo" id="edit_exp_periodo" class="form-control" required></div>
            <div class="form-group"><label>Descrição</label><textarea name="descricao" id="edit_exp_descricao" class="form-control"></textarea></div>
            <button type="submit" class="btn btn-submit">Atualizar Experiência</button>
        </form>
    </div>
</div>

<!-- Modal Adicionar Formação -->
<div id="modalAddFormacao" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalAddFormacao')">&times;</span>
        <h2>Adicionar Formação</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="add_formacao">
            <div class="form-group"><label>Instituição</label><input type="text" name="instituicao" class="form-control" required></div>
            <div class="form-group"><label>Curso</label><input type="text" name="curso" class="form-control" required></div>
            <div class="form-group"><label>Período</label><input type="text" name="periodo" class="form-control" required></div>
            <button type="submit" class="btn btn-submit">Salvar Formação</button>
        </form>
    </div>
</div>

<!-- Modal Editar Formação -->
<div id="modalEditFormacao" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEditFormacao')">&times;</span>
        <h2>Editar Formação</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="edit_formacao">
            <input type="hidden" name="id" id="edit_form_id">
            <div class="form-group"><label>Instituição</label><input type="text" name="instituicao" id="edit_form_instituicao" class="form-control" required></div>
            <div class="form-group"><label>Curso</label><input type="text" name="curso" id="edit_form_curso" class="form-control" required></div>
            <div class="form-group"><label>Período</label><input type="text" name="periodo" id="edit_form_periodo" class="form-control" required></div>
            <button type="submit" class="btn btn-submit">Atualizar Formação</button>
        </form>
    </div>
</div>

<!-- Modal Adicionar Competencia -->
<div id="modalAddCompetencia" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalAddCompetencia')">&times;</span>
        <h2>Adicionar Competência</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="add_competencia">
            <div class="form-group"><label>Nome da Competência (Ex: PHP, Liderança)</label><input type="text" name="nome" class="form-control" required></div>
            <button type="submit" class="btn btn-submit">Salvar Competência</button>
        </form>
    </div>
</div>

<script>
    function abrirModal(id) {
        document.getElementById(id).style.display = 'block';
    }

    function fecharModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function abrirModalEditPerfil(btn) {
        document.getElementById('edit_perfil_nome').value = btn.getAttribute('data-nome');
        document.getElementById('edit_perfil_bio').value = btn.getAttribute('data-bio');
        document.getElementById('edit_perfil_dados').value = btn.getAttribute('data-dados');
        abrirModal('modalEditPerfil');
    }

    function abrirModalEditExperiencia(btn) {
        document.getElementById('edit_exp_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_exp_cargo').value = btn.getAttribute('data-cargo');
        document.getElementById('edit_exp_empresa').value = btn.getAttribute('data-empresa');
        document.getElementById('edit_exp_periodo').value = btn.getAttribute('data-periodo');
        document.getElementById('edit_exp_descricao').value = btn.getAttribute('data-descricao');
        abrirModal('modalEditExperiencia');
    }

    function abrirModalEditFormacao(btn) {
        document.getElementById('edit_form_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_form_instituicao').value = btn.getAttribute('data-instituicao');
        document.getElementById('edit_form_curso').value = btn.getAttribute('data-curso');
        document.getElementById('edit_form_periodo').value = btn.getAttribute('data-periodo');
        abrirModal('modalEditFormacao');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>

</body>
</html>