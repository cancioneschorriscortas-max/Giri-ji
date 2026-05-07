<?php
$archivo = 'diccionario.json';
$datos = [];

if (file_exists($archivo)) {
    $datos = json_decode(file_get_contents($archivo), true);
}

$entradas = $datos['entradas'] ?? [];
$meta = $datos['meta'] ?? [];
$reglas = $datos['reglas'] ?? [];
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diccionario Giri-ji</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0a0a0f;
            --bg-card: #12121a;
            --bg-input: #1a1a28;
            --bg-hover: #222236;
            --accent: #00e5a0;
            --accent-dim: #00e5a033;
            --warn: #ff6b6b;
            --info: #64b5f6;
            --text: #e8e8f0;
            --text-dim: #8888a0;
            --border: #2a2a3a;
            --radius: 8px;
            --mono: 'JetBrains Mono', monospace;
            --sans: 'Noto Sans', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--sans); background: var(--bg-deep); color: var(--text); min-height: 100vh; }

        .header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 { font-family: var(--mono); font-size: 1.4rem; color: var(--accent); }
        .header h1 span { color: var(--text-dim); font-weight: 400; }

        .btn {
            font-family: var(--mono);
            font-size: 0.8rem;
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-input);
            color: var(--text);
        }
        .btn:hover { border-color: var(--accent); color: var(--accent); }

        .container { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }

        /* Search */
        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-bar input {
            flex: 1;
            min-width: 200px;
            font-family: var(--sans);
            font-size: 1rem;
            padding: 12px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
        }
        .search-bar input:focus { outline: none; border-color: var(--accent); }

        .search-bar select {
            font-family: var(--sans);
            padding: 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
        }

        /* Stats mini */
        .meta-bar {
            font-family: var(--mono);
            font-size: 0.75rem;
            color: var(--text-dim);
            margin-bottom: 20px;
        }

        /* Category */
        .cat-section { margin-bottom: 32px; }
        .cat-header {
            font-family: var(--mono);
            font-size: 1.1rem;
            color: var(--accent);
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cat-count {
            font-size: 0.75rem;
            color: var(--text-dim);
            background: var(--bg-input);
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* Entry card */
        .entry {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin-bottom: 8px;
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 16px;
            align-items: start;
            transition: border-color 0.2s;
        }
        .entry:hover { border-color: var(--accent); }

        .entry-emoji {
            font-size: 2.2rem;
            text-align: center;
            padding: 8px 0;
        }

        .entry-info h4 {
            font-size: 1rem;
            margin-bottom: 4px;
        }
        .entry-info h4 .cast {
            font-weight: 400;
            color: var(--text-dim);
            font-size: 0.85rem;
        }

        .entry-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .tag {
            font-family: var(--mono);
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 20px;
            background: var(--bg-input);
            color: var(--text-dim);
            border: 1px solid var(--border);
        }
        .tag.accent { border-color: var(--accent); color: var(--accent); }
        .tag.info { border-color: var(--info); color: var(--info); }
        .tag.warn { border-color: var(--warn); color: var(--warn); }

        .entry-ejemplos {
            margin-top: 8px;
            font-size: 0.8rem;
        }
        .entry-ejemplos .ej {
            font-family: var(--mono);
            color: var(--text-dim);
            padding: 3px 0;
        }
        .entry-ejemplos .ej span { color: var(--accent); }

        .entry-logica {
            font-size: 0.75rem;
            color: var(--text-dim);
            margin-top: 6px;
            font-style: italic;
        }

        .entry-meta {
            font-family: var(--mono);
            font-size: 0.65rem;
            color: var(--text-dim);
            text-align: right;
        }

        .entry-derivaciones {
            font-size: 0.8rem;
            color: var(--info);
            margin-top: 4px;
            font-family: var(--mono);
        }

        .no-results {
            text-align: center;
            color: var(--text-dim);
            padding: 40px;
            font-family: var(--mono);
        }

        @media (max-width: 600px) {
            .entry { grid-template-columns: 60px 1fr; }
            .entry-meta { grid-column: span 2; text-align: left; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📖 Diccionario <span>Giri-ji</span></h1>
    <div>
        <a href="index.html" class="btn">🛠️ Engadir entradas</a>
        <a href="reglas.php" class="btn">📜 Regras</a>
    </div>
</div>

<div class="container">

    <div class="meta-bar">
        <?php if ($meta): ?>
            v<?= $meta['version'] ?? '?' ?> · 
            <?= $meta['total_entradas'] ?? 0 ?> entradas · 
            Modificado: <?= $meta['modificado'] ?? '?' ?>
        <?php endif; ?>
    </div>

    <div class="search-bar">
        <input type="text" id="search" placeholder="🔍 Buscar por palabra, emoji ou lóxica...">
        <select id="filter-cat">
            <option value="">Todas as categorías</option>
            <?php foreach (array_keys($entradas) as $cat): ?>
                <option value="<?= $cat ?>"><?= strtoupper($cat) ?> (<?= count($entradas[$cat]) ?>)</option>
            <?php endforeach; ?>
        </select>
        <select id="filter-tipo">
            <option value="">Todos os tipos</option>
            <option value="raiz">🌱 Raíz</option>
            <option value="composicion">🔗 Composición</option>
            <option value="operador">⚙️ Operador</option>
            <option value="modificador">➕ Modificador</option>
        </select>
        <select id="filter-modo">
            <option value="">Todos os modos</option>
            <option value="entidad">📦 Entidade</option>
            <option value="accion">↗️ Acción</option>
            <option value="estado">😶 Estado</option>
            <option value="atributo">↓ Atributo</option>
            <option value="evento">🌧️ Evento</option>
        </select>
    </div>

    <div id="results">
    <?php if (empty($entradas)): ?>
        <div class="no-results">Non hai entradas aínda. <a href="index.html" style="color:var(--accent)">Engade a primeira!</a></div>
    <?php else: ?>
        <?php foreach ($entradas as $cat => $lista): ?>
            <div class="cat-section" data-cat="<?= $cat ?>">
                <div class="cat-header">
                    <span><?= strtoupper($cat) ?></span>
                    <span class="cat-count"><?= count($lista) ?></span>
                </div>
                <?php foreach ($lista as $e): ?>
                    <div class="entry" 
                         data-galego="<?= strtolower($e['galego'] ?? '') ?>"
                         data-cast="<?= strtolower($e['castellano'] ?? '') ?>"
                         data-emoji="<?= $e['emoji'] ?? '' ?>"
                         data-tipo="<?= $e['tipo_simbolo'] ?? '' ?>"
                         data-modo="<?= $e['modo'] ?? '' ?>"
                         data-logica="<?= strtolower($e['logica'] ?? '') ?>">
                        
                        <div class="entry-emoji"><?= $e['emoji'] ?? '?' ?></div>
                        
                        <div class="entry-info">
                            <h4>
                                <?= $e['galego'] ?? '' ?>
                                <?php if (!empty($e['castellano'])): ?>
                                    <span class="cast">(<?= $e['castellano'] ?>)</span>
                                <?php endif; ?>
                            </h4>
                            
                            <div class="entry-tags">
                                <span class="tag accent"><?= $e['tipo_simbolo'] ?? 'raiz' ?></span>
                                <span class="tag info"><?= $e['modo'] ?? '' ?></span>
                                <span class="tag">N<?= $e['nivel'] ?? '4' ?></span>
                                <?php 
                                $props = $e['propiedades'] ?? [];
                                if (!empty($props['lleva_accion'])) echo '<span class="tag accent">↗️</span>';
                                if (!empty($props['es_pasivo'])) echo '<span class="tag warn">pasivo</span>';
                                if (!empty($props['es_contenedor'])) echo '<span class="tag">[ ]</span>';
                                if (!empty($props['permite_negacion'])) echo '<span class="tag">❌→concepto</span>';
                                ?>
                            </div>

                            <?php if (!empty($e['composicion']['derivaciones'])): ?>
                                <div class="entry-derivaciones">↳ <?= $e['composicion']['derivaciones'] ?></div>
                            <?php endif; ?>

                            <?php if (!empty($e['ejemplos'])): ?>
                                <div class="entry-ejemplos">
                                    <?php foreach ($e['ejemplos'] as $ej): ?>
                                        <div class="ej"><span><?= $ej['giriji'] ?? '' ?></span> → <?= $ej['traduccion'] ?? '' ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($e['logica'])): ?>
                                <div class="entry-logica">"<?= $e['logica'] ?>"</div>
                            <?php endif; ?>
                        </div>

                        <div class="entry-meta">
                            <?= $e['fecha_creacion'] ?? '' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>

<script>
// Filtrado en tiempo real
const search = document.getElementById('search');
const filterCat = document.getElementById('filter-cat');
const filterTipo = document.getElementById('filter-tipo');
const filterModo = document.getElementById('filter-modo');

function filtrar() {
    const q = search.value.toLowerCase();
    const cat = filterCat.value;
    const tipo = filterTipo.value;
    const modo = filterModo.value;

    document.querySelectorAll('.cat-section').forEach(sec => {
        const secCat = sec.dataset.cat;
        if (cat && secCat !== cat) { sec.style.display = 'none'; return; }
        
        let visible = 0;
        sec.querySelectorAll('.entry').forEach(entry => {
            let show = true;

            if (q) {
                const text = (entry.dataset.galego + ' ' + entry.dataset.cast + ' ' + entry.dataset.emoji + ' ' + entry.dataset.logica);
                if (!text.includes(q)) show = false;
            }
            if (tipo && entry.dataset.tipo !== tipo) show = false;
            if (modo && entry.dataset.modo !== modo) show = false;

            entry.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        sec.style.display = visible > 0 ? '' : 'none';
    });
}

search.addEventListener('input', filtrar);
filterCat.addEventListener('change', filtrar);
filterTipo.addEventListener('change', filtrar);
filterModo.addEventListener('change', filtrar);
</script>

</body>
</html>
