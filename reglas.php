<?php
$archivo = 'diccionario.json';
$reglas = [];

if (file_exists($archivo)) {
    $datos = json_decode(file_get_contents($archivo), true);
    $reglas = $datos['reglas'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regras Giri-ji</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0a0a0f;
            --bg-card: #12121a;
            --bg-input: #1a1a28;
            --accent: #00e5a0;
            --accent-dim: #00e5a033;
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
        }
        .header h1 { font-family: var(--mono); font-size: 1.4rem; color: var(--accent); }
        .btn {
            font-family: var(--mono); font-size: 0.8rem; padding: 8px 16px;
            border: 1px solid var(--border); border-radius: var(--radius);
            cursor: pointer; text-decoration: none; background: var(--bg-input); color: var(--text);
        }
        .btn:hover { border-color: var(--accent); color: var(--accent); }

        .container { max-width: 800px; margin: 0 auto; padding: 30px 20px; }

        .regla {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 12px;
        }
        .regla-key {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .regla-val {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text);
        }

        .no-data {
            text-align: center;
            color: var(--text-dim);
            padding: 40px;
            font-family: var(--mono);
        }

        .nivel-section {
            margin-bottom: 32px;
        }
        .nivel-title {
            font-family: var(--mono);
            font-size: 1.1rem;
            color: var(--accent);
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📜 Regras Giri-ji</h1>
    <div>
        <a href="index.html" class="btn">🛠️ Lab</a>
        <a href="ver.php" class="btn">📖 Diccionario</a>
    </div>
</div>

<div class="container">

    <!-- Jerarquía de niveles (hardcoded, es fundacional) -->
    <div class="nivel-section">
        <div class="nivel-title">🧠 Xerarquía de niveis</div>
        <div class="regla">
            <div class="regla-key">Nivel 1 — Núcleo</div>
            <div class="regla-val">👤 (entidade), ↗️ (acción), 🌀 (concepto), 🔗 (relación), ❌ (negación)</div>
        </div>
        <div class="regla">
            <div class="regla-key">Nivel 2 — Mundo físico</div>
            <div class="regla-val">📍 (posición), 💓 (vida)</div>
        </div>
        <div class="regla">
            <div class="regla-key">Nivel 3 — Ontoloxía</div>
            <div class="regla-val">✳️ (existir)</div>
        </div>
        <div class="regla">
            <div class="regla-key">Nivel 4 — Vocabulario</div>
            <div class="regla-val">Todas as raíces temáticas, composicións e derivacións</div>
        </div>
    </div>

    <!-- Reglas del JSON -->
    <div class="nivel-section">
        <div class="nivel-title">🔑 Regras do sistema</div>
        <?php if (empty($reglas)): ?>
            <div class="no-data">Non hai regras gardadas. Garda unha entrada para inicializar o sistema.</div>
        <?php else: ?>
            <?php foreach ($reglas as $key => $val): ?>
                <div class="regla">
                    <div class="regla-key"><?= htmlspecialchars($key) ?></div>
                    <div class="regla-val"><?= htmlspecialchars($val) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Reglas fundamentales (hardcoded) -->
    <div class="nivel-section">
        <div class="nivel-title">⚠️ Regras de ouro</div>
        <div class="regla">
            <div class="regla-key">Regra 1</div>
            <div class="regla-val">1 símbolo = 1 función</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 2</div>
            <div class="regla-val">Se non se entende en 1 segundo → non vale</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 3</div>
            <div class="regla-val">Non engadir significado que non estea nos símbolos</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 4</div>
            <div class="regla-val">En Giri-ji, o espazo tamén significa (❌🤝 = traición, ❌ 🤝↗️ = non acordar)</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 5</div>
            <div class="regla-val">Cando necesitas un oposto, crea un símbolo (🔒), non o forzes con negación</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 6</div>
            <div class="regla-val">↗️ = comportamento inherente. Non todo símbolo ten forma ↗️</div>
        </div>
        <div class="regla">
            <div class="regla-key">Regra 7</div>
            <div class="regla-val">Sen ↗️ = cousa. Con ↗️ = o que fai esa cousa</div>
        </div>
        <div class="regla">
            <div class="regra-key">Regra 8</div>
            <div class="regla-val">❌ + entidade pegado = concepto novo (se ten significado claro). ❌ separado + verbo = negación</div>
        </div>
    </div>
</div>

</body>
</html>
