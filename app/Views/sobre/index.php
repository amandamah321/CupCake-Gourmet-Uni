<section class="sobre-historia">
    <div class="sobre-historia-imagem">
        <img
            src="/img/confeiteira.jpg"
            alt="Confeiteira preparando cupcakes">
    </div>

    <div class="sobre-historia-conteudo">
        <span class="sobre-subtitulo">Conheça nossa essência</span>

        <h1>Nossa História</h1>

        <p>
            A CupCake Gourmet nasceu da paixão pela confeitaria
            e pelo desejo de transformar momentos especiais em
            experiências ainda mais doces.
        </p>

        <p>
            Cada produto é preparado com atenção aos detalhes,
            ingredientes selecionados e muito carinho, valorizando
            o sabor e a qualidade em cada criação.
        </p>

        <p>
            Nosso objetivo é levar mais doçura ao dia a dia dos
            nossos clientes, oferecendo cupcakes especiais para
            todos os momentos.
        </p>
    </div>
</section>

<section class="sobre-valores">
    <h2>Nossos Valores</h2>

    <div class="sobre-valores-lista">
        <article class="sobre-valor-card">
            <span class="sobre-valor-icone">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 21h32l-3 20H11L8 21Z" />
                    <path d="M15 21l9-14 9 14" />
                    <path d="M17 27v8M24 27v8M31 27v8" />
                    <path d="M5 21h38" />
                </svg>
            </span>
            <h3>Ingredientes de qualidade</h3>
        </article>

        <article class="sobre-valor-card">
            <span class="sobre-valor-icone">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M24 41S5 29 5 16C5 5 19 2 24 13 29 2 43 5 43 16c0 13-19 25-19 25Z" />
                </svg>
            </span>
            <h3>Feitos com amor</h3>
        </article>

        <article class="sobre-valor-card">
            <span class="sobre-valor-icone">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M40 7C21 7 9 12 8 27c0 8 6 13 13 11C36 34 40 7 40 7Z" />
                    <path d="M7 43c7-13 15-20 27-29" />
                </svg>
            </span>
            <h3>Produção artesanal</h3>
        </article>

        <article class="sobre-valor-card">
            <span class="sobre-valor-icone">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12h25v24H4z" />
                    <path d="M29 21h9l7 8v7H29z" />
                    <circle cx="13" cy="37" r="4" />
                    <circle cx="37" cy="37" r="4" />
                </svg>
            </span>
            <h3>Entrega segura</h3>
        </article>
    </div>
</section>

<section class="secao-avaliacoes">
    <div class="avaliacoes-conteudo">
        <h2>Avaliações</h2>

        <div class="avaliacoes">
            <?php foreach (($avaliacoes ?? []) as $avaliacao): ?>
                <article class="avaliacao-card">
                    <div class="avaliacao-estrelas" aria-label="<?= (int) $avaliacao['estrelas'] ?> de 5 estrelas">
                        <?= str_repeat('★', (int) $avaliacao['estrelas']) ?><?= str_repeat('☆', 5 - (int) $avaliacao['estrelas']) ?>
                    </div>

                    <p class="avaliacao-comentario">
                        “<?= htmlspecialchars($avaliacao['comentario'] ?? '', ENT_QUOTES, 'UTF-8') ?>”
                    </p>

                    <div class="avaliacao-cliente">
                        — <?= htmlspecialchars($avaliacao['nome_exibicao'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>