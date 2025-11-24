const tabelaCorpo = document.getElementById('corpo-tabela-produtos');

const urlDados = 'http://localhost/AlbatrozWeb/Albatroz-web/backend/api.php?resource=produtos';



function carregarCatalogoJSON() {

    fetch(urlDados)
        .then(response => {

            if (!response.ok) {
                throw new Error(`Erro ao buscar dados: ${response.statusText}`);
            }

            return response.json();
        })

        .then(data => {
            renderizarProdutos(data);
        })

        .catch(error => {
            console.error('Houve um erro ao carregar o catálogo:', error);
            tabelaCorpo.innerHTML = `<tr><td colspan="3">Erro ao carregar os produtos.</td></tr>`;
        });

}

function renderizarProdutos(produtos) {
    let htmlProdutos = '';

    produtos.forEach(produto => {
        htmlProdutos += `
        <tr>
            <td><img src="${produto.imagem}" alt="${produto.nome}" width="70"></td>
            <td>${produto.nome}</td>
            <td>${produto.preco}</td>
            <td>${produto.disponibilidade}</td>
        </tr>
        `;
    });

    tabelaCorpo.innerHTML = htmlProdutos;
}



// 4. Inicia o carregamento

carregarCatalogoJSON();