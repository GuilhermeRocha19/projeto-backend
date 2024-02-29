<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\CompanyService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController
{
    private ProductService $productService;
    private CompanyService $companyService;
    
    public function __construct()
    {
        $this->productService = new ProductService();
        $this->companyService = new CompanyService();
    }
    
    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $id_product = $request->getHeader('id')[0];
        
        $data = [];
        $data[] = [
            'Id do usuário',
            'Nome do usuário',
            'Nome do Produto',
            'Tipo de alteração',
            'Data'
        ];
        

        $stm = $this->productService->getLog($adminUserId,$id_product);
        $productLogs = $stm->fetchAll();

        foreach ($productLogs as $i => $log) {
            
            $data[$i+1][] = $log->admin_user_id;
            $data[$i+1][] = $log->name;
            $data[$i+1][] = $log->product;
            $data[$i+1][] = $log->action;
            $data[$i+1][] = date('d/m/Y H:i:s', strtotime($log->timestamp));

        }
        
        $report = "<table style='font-size: 10px;'>";
        foreach ($data as $row) {
            $report .= "<tr>";
            foreach ($row as $column) {
                $report .= "<td>{$column}</td>";
            }
            $report .= "</tr>";
        }
        $report .= "</table>";
        
        $response->getBody()->write($report);
        return $response->withStatus(200)->withHeader('Content-Type', 'text/html');
    }
}
