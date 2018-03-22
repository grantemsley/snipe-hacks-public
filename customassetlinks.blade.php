<tr>
  <td>Custom Links</td>
  <td>
    <div><a target="_blank" href="/custom/labels/printlabel.php?id={{ $asset->id }}">Print asset tag label</a></div>
    @if (isset($asset->_snipeit_dell_service_tag_11))
      <div><a target="_blank" href="/custom/dellassettag/warrantylookup.php?id={{ $asset->id }}">Dell warranty/configuration lookup</a></div>
    @endif
  </td>
</tr>
