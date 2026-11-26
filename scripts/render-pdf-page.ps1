param(
    [Parameter(Mandatory = $true)][string]$Path,
    [Parameter(Mandatory = $true)][string]$Out,
    [int]$PageIndex = 0
)

$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.Runtime.WindowsRuntime

$asTaskGeneric = ([System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object {
    $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 -and $_.GetParameters()[0].ParameterType.Name -eq 'IAsyncOperation`1'
})[0]

function Await($WinRtTask, $ResultType) {
    $asTask = $asTaskGeneric.MakeGenericMethod($ResultType)
    $netTask = $asTask.Invoke($null, @($WinRtTask))
    $netTask.Wait(-1) | Out-Null
    $netTask.Result
}

$asActionTask = ([System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object {
    $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 -and $_.GetParameters()[0].ParameterType.Name -eq 'IAsyncAction'
})[0]

function AwaitAction($WinRtAction) {
    $netTask = $asActionTask.Invoke($null, @($WinRtAction))
    $netTask.Wait(-1) | Out-Null
}

[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null
[Windows.Data.Pdf.PdfDocument, Windows.Data.Pdf, ContentType = WindowsRuntime] | Out-Null
[Windows.Data.Pdf.PdfPageRenderOptions, Windows.Data.Pdf, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.Streams.InMemoryRandomAccessStream, Windows.Storage.Streams, ContentType = WindowsRuntime] | Out-Null

$file = Await ([Windows.Storage.StorageFile]::GetFileFromPathAsync($Path)) ([Windows.Storage.StorageFile])
$document = Await ([Windows.Data.Pdf.PdfDocument]::LoadFromFileAsync($file)) ([Windows.Data.Pdf.PdfDocument])

if ($PageIndex -ge $document.PageCount) {
    $PageIndex = 0
}

$page = $document.GetPage([uint32]$PageIndex)
$stream = [Windows.Storage.Streams.InMemoryRandomAccessStream]::new()

# Rendu plafonné à 800 px de large : largement suffisant pour l'aperçu et la
# fiche mémoire, tout en gardant des images légères à charger.
$options = [Windows.Data.Pdf.PdfPageRenderOptions]::new()
$options.DestinationWidth = 800

AwaitAction ($page.RenderToStreamAsync($stream, $options))

$reader = [Windows.Storage.Streams.DataReader]::new($stream.GetInputStreamAt(0))
Await ($reader.LoadAsync([uint32]$stream.Size)) ([uint32]) | Out-Null
$bytes = New-Object byte[] $stream.Size
$reader.ReadBytes($bytes)

[System.IO.File]::WriteAllBytes($Out, $bytes)
