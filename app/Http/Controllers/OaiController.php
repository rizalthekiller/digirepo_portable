<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thesis;
use App\Models\Setting;
use Illuminate\Support\Facades\Response;

class OaiController extends Controller
{
    public function index(Request $request)
    {
        $verb = $request->input('verb');
        
        $response = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $response .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">' . "\n";
        
        $response .= '<responseDate>' . gmdate('Y-m-d\TH:i:s\Z') . '</responseDate>' . "\n";
        $response .= '<request verb="' . htmlspecialchars($verb ?? '') . '">' . url('/oai') . '</request>' . "\n";

        switch ($verb) {
            case 'Identify':
                $response .= $this->identify();
                break;
            case 'ListMetadataFormats':
                $response .= $this->listMetadataFormats();
                break;
            case 'ListRecords':
                $response .= $this->listRecords($request);
                break;
            case 'ListIdentifiers':
                $response .= $this->listIdentifiers($request);
                break;
            case 'GetRecord':
                $response .= $this->getRecord($request);
                break;
            default:
                $response .= '<error code="badVerb">Illegal OAI verb</error>' . "\n";
                break;
        }

        $response .= '</OAI-PMH>';

        return Response::make($response, 200, ['Content-Type' => 'text/xml']);
    }

    private function identify()
    {
        $siteName = Setting::get('site_name', 'DigiRepo');
        $siteEmail = Setting::get('site_email', 'admin@digirepo.local');
        $earliestDatestamp = Thesis::where('status', 'approved')->min('created_at');
        if (!$earliestDatestamp) {
            $earliestDatestamp = now();
        } else {
            $earliestDatestamp = \Carbon\Carbon::parse($earliestDatestamp);
        }

        return '<Identify>' . "\n" .
               '<repositoryName>' . htmlspecialchars($siteName) . '</repositoryName>' . "\n" .
               '<baseURL>' . url('/oai') . '</baseURL>' . "\n" .
               '<protocolVersion>2.0</protocolVersion>' . "\n" .
               '<adminEmail>' . htmlspecialchars($siteEmail) . '</adminEmail>' . "\n" .
               '<earliestDatestamp>' . $earliestDatestamp->format('Y-m-d\TH:i:s\Z') . '</earliestDatestamp>' . "\n" .
               '<deletedRecord>no</deletedRecord>' . "\n" .
               '<granularity>YYYY-MM-DDThh:mm:ssZ</granularity>' . "\n" .
               '</Identify>' . "\n";
    }

    private function listMetadataFormats()
    {
        return '<ListMetadataFormats>' . "\n" .
               '<metadataFormat>' . "\n" .
               '<metadataPrefix>oai_dc</metadataPrefix>' . "\n" .
               '<schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>' . "\n" .
               '<metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>' . "\n" .
               '</metadataFormat>' . "\n" .
               '</ListMetadataFormats>' . "\n";
    }

    private function listRecords(Request $request)
    {
        $metadataPrefix = $request->input('metadataPrefix');
        if ($metadataPrefix !== 'oai_dc') {
            return '<error code="cannotDisseminateFormat">Format not supported</error>' . "\n";
        }

        $query = Thesis::where('status', 'approved');

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }
        if ($request->has('until')) {
            $query->where('created_at', '<=', $request->input('until'));
        }

        $theses = $query->get();

        if ($theses->isEmpty()) {
            return '<error code="noRecordsMatch">No matching records</error>' . "\n";
        }

        $xml = '<ListRecords>' . "\n";
        foreach ($theses as $thesis) {
            $xml .= $this->createRecordNode($thesis);
        }
        $xml .= '</ListRecords>' . "\n";

        return $xml;
    }

    private function listIdentifiers(Request $request)
    {
        $metadataPrefix = $request->input('metadataPrefix');
        if ($metadataPrefix !== 'oai_dc') {
            return '<error code="cannotDisseminateFormat">Format not supported</error>' . "\n";
        }

        $query = Thesis::where('status', 'approved');

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }
        if ($request->has('until')) {
            $query->where('created_at', '<=', $request->input('until'));
        }

        $theses = $query->get();

        if ($theses->isEmpty()) {
            return '<error code="noRecordsMatch">No matching records</error>' . "\n";
        }

        $xml = '<ListIdentifiers>' . "\n";
        foreach ($theses as $thesis) {
            $xml .= '<header>' . "\n" .
                    '<identifier>oai:' . parse_url(url('/'), PHP_URL_HOST) . ':' . $thesis->id . '</identifier>' . "\n" .
                    '<datestamp>' . $thesis->created_at->format('Y-m-d\TH:i:s\Z') . '</datestamp>' . "\n" .
                    '</header>' . "\n";
        }
        $xml .= '</ListIdentifiers>' . "\n";

        return $xml;
    }

    private function getRecord(Request $request)
    {
        $identifier = $request->input('identifier');
        $metadataPrefix = $request->input('metadataPrefix');

        if ($metadataPrefix !== 'oai_dc') {
            return '<error code="cannotDisseminateFormat">Format not supported</error>' . "\n";
        }

        preg_match('/oai:.*?:(\d+)/', $identifier, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            return '<error code="idDoesNotExist">Invalid identifier format</error>' . "\n";
        }

        $thesis = Thesis::where('status', 'approved')->find($id);

        if (!$thesis) {
            return '<error code="idDoesNotExist">Record not found</error>' . "\n";
        }

        return '<GetRecord>' . "\n" . $this->createRecordNode($thesis) . '</GetRecord>' . "\n";
    }

    private function createRecordNode($thesis)
    {
        $identifier = 'oai:' . parse_url(url('/'), PHP_URL_HOST) . ':' . $thesis->id;
        $xml = '<record>' . "\n";
        $xml .= '<header>' . "\n";
        $xml .= '<identifier>' . $identifier . '</identifier>' . "\n";
        $xml .= '<datestamp>' . $thesis->created_at->format('Y-m-d\TH:i:s\Z') . '</datestamp>' . "\n";
        $xml .= '</header>' . "\n";
        
        $xml .= '<metadata>' . "\n";
        $xml .= '<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">' . "\n";
        
        $xml .= '<dc:title>' . htmlspecialchars($thesis->title ?? '') . '</dc:title>' . "\n";
        $xml .= '<dc:creator>' . htmlspecialchars($thesis->user->name ?? 'Unknown') . '</dc:creator>' . "\n";
        $xml .= '<dc:subject>' . htmlspecialchars($thesis->keywords ?? '') . '</dc:subject>' . "\n";
        $xml .= '<dc:description>' . htmlspecialchars($thesis->abstract ?? '') . '</dc:description>' . "\n";
        $xml .= '<dc:publisher>' . htmlspecialchars(Setting::get('site_institution', 'Universitas')) . '</dc:publisher>' . "\n";
        $xml .= '<dc:contributor>' . htmlspecialchars($thesis->supervisor_name ?? '') . '</dc:contributor>' . "\n";
        $xml .= '<dc:date>' . htmlspecialchars($thesis->year ?? '') . '</dc:date>' . "\n";
        $xml .= '<dc:type>' . htmlspecialchars($thesis->type ?? '') . '</dc:type>' . "\n";
        $xml .= '<dc:format>application/pdf</dc:format>' . "\n";
        $xml .= '<dc:identifier>' . route('theses.show', $thesis->id) . '</dc:identifier>' . "\n";
        $xml .= '<dc:language>id</dc:language>' . "\n";
        
        $xml .= '</oai_dc:dc>' . "\n";
        $xml .= '</metadata>' . "\n";
        $xml .= '</record>' . "\n";

        return $xml;
    }
}
