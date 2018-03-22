//----------------------------------------------------------------------------
// Originally PreviewAndPrintLabel.js from Dymo
// Modified by Grant Emsley
//----------------------------------------------------------------------------

(function () {
    // stores loaded label info
    var label;

    // called when the document completly loaded
    function onload() {
        var qrcode = document.getElementById('qrcode');
        var assetnumber = document.getElementById('assetnumber');
        var date = document.getElementById('date');

        // Generates label preview and updates corresponend <img> element
        // Note: this does not work in IE 6 & 7 because they don't support data urls
        // if you want previews in IE 6 & 7 you have to do it on the server side
        function updatePreview() {
            if (!label)
                return;

            label.setObjectText('QRCODE', qrcode.value);
            label.setObjectText('ASSETNUMBER', 'Asset ' + assetnumber.value);
          
            label.setObjectText('DATE', date.value);
            console.log(label.getObjectNames());

            var pngData = label.render();

            var labelImage = document.getElementById('labelImage');
            labelImage.src = "data:image/png;base64," + pngData;
        }

        // loads all supported printers into a combo box 
        function loadPrinters() {
            var printers = dymo.label.framework.getPrinters();
            if (printers.length == 0) {
                alert("No DYMO printers are installed. Install DYMO printers.");
                return;
            }

            for (var i = 0; i < printers.length; i++) {
                var printer = printers[i];
                if (printer.printerType == "LabelWriterPrinter") {
                    var printerName = printer.name;

                    var option = document.createElement('option');
                    option.value = printerName;
                    option.appendChild(document.createTextNode(printerName));
                    printersSelect.appendChild(option);
                }
            }
        }

        // prints the label
        printButton.onclick = function () {
            try {
                if (!label) {
                    alert("Load label before printing");
                    return;
                }

                //alert(printersSelect.value);
                label.print(printersSelect.value);
                //label.print("unknown printer");
            }
            catch (e) {
                alert(e.message || e);
            }
        }
        function getLabelXml() {

            var labelXml = `<?xml version="1.0" encoding="utf-8"?>
<DieCutLabel Version="8.0" Units="twips">
  <PaperOrientation>Landscape</PaperOrientation>
  <Id>Address</Id>
  <IsOutlined>false</IsOutlined>
  <PaperName>30252 Address</PaperName>
  <DrawCommands>
    <RoundRectangle X="0" Y="0" Width="1581" Height="5040" Rx="270" Ry="270" />
  </DrawCommands>
  <ObjectInfo>
    <ImageObject>
      <Name>GRAPHIC</Name>
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
      <LinkedObjectName />
      <Rotation>Rotation0</Rotation>
      <IsMirrored>False</IsMirrored>
      <IsVariable>False</IsVariable>
      <GroupID>-1</GroupID>
      <IsOutlined>False</IsOutlined>
      <ScaleMode>Uniform</ScaleMode>
      <BorderWidth>0</BorderWidth>
      <BorderColor Alpha="255" Red="0" Green="0" Blue="0" />
      <HorizontalAlignment>Center</HorizontalAlignment>
      <VerticalAlignment>Center</VerticalAlignment>
    </ImageObject>
    <Bounds X="1444.75" Y="118" Width="3313.25" Height="730" />
  </ObjectInfo>
  <ObjectInfo>
    <BarcodeObject>
      <Name>QRCODE</Name>
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
      <LinkedObjectName />
      <Rotation>Rotation0</Rotation>
      <IsMirrored>False</IsMirrored>
      <IsVariable>False</IsVariable>
      <GroupID>-1</GroupID>
      <IsOutlined>False</IsOutlined>
      <Text></Text>
      <Type>QRCode</Type>
      <Size>Large</Size>
      <TextPosition>None</TextPosition>
      <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
      <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
      <TextEmbedding>None</TextEmbedding>
      <ECLevel>0</ECLevel>
      <HorizontalAlignment>Center</HorizontalAlignment>
      <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />
    </BarcodeObject>
    <Bounds X="331" Y="57.9999999999999" Width="1140" Height="1435" />
  </ObjectInfo>
  <ObjectInfo>
    <TextObject>
      <Name>ASSETNUMBER</Name>
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
      <LinkedObjectName />
      <Rotation>Rotation0</Rotation>
      <IsMirrored>False</IsMirrored>
      <IsVariable>False</IsVariable>
      <GroupID>-1</GroupID>
      <IsOutlined>False</IsOutlined>
      <HorizontalAlignment>Center</HorizontalAlignment>
      <VerticalAlignment>Middle</VerticalAlignment>
      <TextFitMode>AlwaysFit</TextFitMode>
      <UseFullFontHeight>True</UseFullFontHeight>
      <Verticalized>False</Verticalized>
      <StyledText>
        <Element>
          <String xml:space="preserve">Asset 99999</String>
          <Attributes>
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />
          </Attributes>
        </Element>
      </StyledText>
    </TextObject>
    <Bounds X="1383" Y="735" Width="3570" Height="525" />
  </ObjectInfo>
  <ObjectInfo>
    <TextObject>
      <Name>DATE</Name>
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
      <LinkedObjectName />
      <Rotation>Rotation0</Rotation>
      <IsMirrored>False</IsMirrored>
      <IsVariable>False</IsVariable>
      <GroupID>-1</GroupID>
      <IsOutlined>False</IsOutlined>
      <HorizontalAlignment>Center</HorizontalAlignment>
      <VerticalAlignment>Bottom</VerticalAlignment>
      <TextFitMode>AlwaysFit</TextFitMode>
      <UseFullFontHeight>True</UseFullFontHeight>
      <Verticalized>False</Verticalized>
      <StyledText>
        <Element>
          <String xml:space="preserve">2016-01-01</String>
          <Attributes>
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />
          </Attributes>
        </Element>
      </StyledText>
    </TextObject>
    <Bounds X="1443.75" Y="1316.75" Width="3509.25" Height="135" />
  </ObjectInfo>
</DieCutLabel>
`;

            return labelXml;
        }
        function loadLabel() {
            label = dymo.label.framework.openLabelXml(getLabelXml());
            updatePreview();
        }

        loadLabel();

        // load printers list on startup
        loadPrinters();
    };

    function initTests()
	{
		if(dymo.label.framework.init)
		{
			//dymo.label.framework.trace = true;
			dymo.label.framework.init(onload);
		} else {
			onload();
		}
	}

	// register onload event
	if (window.addEventListener)
		window.addEventListener("load", initTests, false);
	else if (window.attachEvent)
		window.attachEvent("onload", initTests);
	else
		window.onload = initTests;

}());
