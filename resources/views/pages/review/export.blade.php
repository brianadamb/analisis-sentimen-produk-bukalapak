<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Komen Name</th>
      <th>Title</th>
      <th>Konten</th>
      <th>Rate</th>
      <th>Created Date</th>
      <th>Updated Date</th>
    </tr>
  </thead>
      <tbody> 
        @foreach ($review as $key => $item) <tr>
          <td>{{$key+1}}</td>
          <td>{{$item->komen_name}}</td>
          <td>{{$item->title}}</td>
          <td>{{$item->konten}}</td>
          <td> 
            @for($i = 0; $i < $item->rate; $i++) 
                * 
            @endfor 
          </td>
          <td>{{$item->created_at}}</td>
          <td>{{$item->updated_at}}</td>
        </tr> 
        @endforeach 
    </tbody>
</table>