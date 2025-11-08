const tabelaCorpo = document.getElementById('corpo-tabela-produtos');

const urlDados = 'data/dados.json';


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

<td><img src="${produto.DescriçãoVisual.src}" alt="${produto.DescriçãoVisual.alt}" width="70"></td>

<td>${produto.NomeProduto}</td>

<td>${produto.Preço}</td>

<td>${produto.Disponibilidade}</td>

</tr>

`;

    });


    tabelaCorpo.innerHTML = htmlProdutos;

}


// 4. Inicia o carregamento

carregarCatalogoJSON();