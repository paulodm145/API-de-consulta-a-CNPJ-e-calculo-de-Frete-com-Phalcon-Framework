# Sobre este projeto  
Api construída com o **PHALCON FRAMEWORK**.   
Esta aplicação consome os dados de duas API's sendo elas a **[RECEITAWS](https://www.receitaws.com.br/)** e **[FRETERAPIDO](https://freterapido.com/)**    
# Rotas/Endpoints  
    
| Rota            | Metodo  | Descrição                                                    |
|-----------------|---------|--------------------------------------------------------------|
|/api/cnpj/*cnpj* |get      | Retorna um JSON com os dados do CNPJ Informado               | 
|/api/quote       |post     | Retorna um JSON com a cotação do Frete de Empresas Ficticias |  


# Utilização  
Para testar o funcionamento da mesma instalar o PHALCON FRAMEWORK na basta de LIBS do PHP.  
Depois de ter Clonado ou baixado o arquivo ZIP deste repositório você apenas basta colocar dentro da pasta htdocs ou www do **XAMPP** ou do **WAMPP** e acessar normalmente pela URL. 
**Formato do JSON para envio no Método POST**
```
{
	"remetente": {
		"cnpj": "17184406000174"
	},
	"destinatario": {
		"tipo_pessoa": 2,
		"cnpj_cpf": "69111653000144",
		"inscricao_estadual": "123456",
		"endereco": {
			"cep": "08465312"
		}
	},
	"volumes": [
		{
			"tipo": 7,
			"sku": "MTC-220-AM",
			"descricao": "Sofá Anjos Confortable 3 Lugares Retrátil E Reclinável Velud",
			"quantidade": 2,
			"altura": 0.52,
			"largura": 0.42,
			"comprimento": 0.30,
			"peso": 29.75,
			"valor": 542.56
		},
		{
			"tipo": 9,
			"sku": "MKV-100-AA",
			"descricao": "Sofá Anjos Confortable 3 Lugares Retrátil E Reclinável Velud",
			"quantidade": 8,
			"altura": 0.50,
			"largura": 0.40,
			"comprimento": 0.30,
			"peso": 30.00,
			"valor": 205.00
		},
		{
			"tipo": 8,
			"sku": "LT-027-MG",
			"descricao": "Smart TV LED 32'' Samsung 32J4300 HD com Conversor Digital 2 HDMI 1 USB Wi-Fi 120Hz",
			"quantidade": 1,
			"altura": 0.90,
			"largura": 1.50,
			"comprimento": 0.10,
			"peso": 3.00,
			"valor": 85.67
		}
	],
	"tipo_frete" : 1,
	"codigo_plataforma": "588604ab3",
	"token": "c8359377969ded682c3dba5cb967c07b"
}
```





